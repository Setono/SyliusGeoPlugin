<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\EventSubscriber;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\SyliusGeoPlugin\EligibilityChecker\RuleEligibilityCheckerInterface;
use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Setono\SyliusGeoPlugin\Repository\RuleRepositoryInterface;
use Setono\SyliusGeoPlugin\UrlGenerator\ChannelUrlGeneratorInterface;
use Setono\SyliusGeoPlugin\UrlGenerator\Route;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RedirectSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    private bool $setCookie = false;

    private const COOKIE_NAME = 'ssg_checked';

    private LoggerInterface $logger;

    private ChannelContextInterface $channelContext;

    private RuleRepositoryInterface $ruleRepository;

    private ChannelUrlGeneratorInterface $urlGenerator;

    private RuleEligibilityCheckerInterface $ruleEligibilityChecker;

    public function __construct(
        ChannelContextInterface $channelContext,
        RuleRepositoryInterface $ruleRepository,
        ChannelUrlGeneratorInterface $urlGenerator,
        RuleEligibilityCheckerInterface $ruleEligibilityChecker
    ) {
        $this->logger = new NullLogger();
        $this->channelContext = $channelContext;
        $this->ruleRepository = $ruleRepository;
        $this->urlGenerator = $urlGenerator;
        $this->ruleEligibilityChecker = $ruleEligibilityChecker;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'redirect',
            KernelEvents::RESPONSE => 'setCookie',
        ];
    }

    public function redirect(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->cookies->has(self::COOKIE_NAME)) {
            return;
        }

        try {
            $rules = $this->ruleRepository->findEnabledBySourceChannel($this->channelContext->getChannel());
        } catch (ChannelNotFoundException $e) {
            return;
        }

        if ([] === $rules) {
            return;
        }

        try {
            $route = Route::fromRequest($request);
        } catch (\InvalidArgumentException $e) {
            return;
        }

        $this->setCookie = true;

        foreach ($rules as $rule) {
            $targetChannel = $rule->getTargetChannel();
            if (null === $targetChannel) {
                continue;
            }

            if ($this->ruleEligibilityChecker->isEligible($rule)) {
                try {
                    $url = $this->urlGenerator->generate($targetChannel, $route->withLocaleCode($rule->getTargetLocale()));
                } catch (UrlGenerationException $e) {
                    $this->logger->error(sprintf(
                        'A visitor matched the rule %s, but the URL generation threw an exception: %s',
                        (string) $rule->getName(),
                        $e->getMessage()
                    ));

                    continue;
                }

                $event->setResponse(new RedirectResponse($url));

                break;
            }
        }
    }

    public function setCookie(ResponseEvent $event): void
    {
        if (!$this->setCookie || !$event->isMainRequest()) {
            return;
        }

        $event->getResponse()->headers->setCookie(
            Cookie::create(self::COOKIE_NAME, '1', new \DateTimeImmutable('+30 days'))
        );
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
