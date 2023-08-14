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
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RedirectSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
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
        ];
    }

    public function redirect(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
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

        foreach ($rules as $rule) {
            $targetChannel = $rule->getTargetChannel();
            if (null === $targetChannel) {
                continue;
            }

            if ($this->ruleEligibilityChecker->isEligible($rule)) {
                try {
                    $url = $this->urlGenerator->generate($targetChannel, $rule->getTargetLocale(), $event->getRequest());
                } catch (UrlGenerationException $e) {
                    $this->logger->error(sprintf(
                        'A visitor matched the rule %s, but the URL generation threw an exception: %s',
                        (string) $rule->getName(),
                        $e->getMessage()
                    ));

                    continue;
                }

                $event->setResponse(new RedirectResponse($url));
                $event->stopPropagation();

                break;
            }
        }
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
