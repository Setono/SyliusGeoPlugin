<?php
declare(strict_types=1);


namespace Setono\SyliusGeoPlugin\EventSubscriber;


use Setono\SyliusGeoPlugin\EligibilityChecker\RuleEligibilityCheckerInterface;
use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Setono\SyliusGeoPlugin\Provider\CountryCodeProviderInterface;
use Setono\SyliusGeoPlugin\Repository\RuleRepositoryInterface;
use Setono\SyliusGeoPlugin\UrlGenerator\UrlGeneratorInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RedirectSubscriber implements EventSubscriberInterface
{
    private ChannelContextInterface $channelContext;
    private RuleRepositoryInterface $ruleRepository;
    private UrlGeneratorInterface $urlGenerator;
    private RuleEligibilityCheckerInterface $ruleEligibilityChecker;

    public function __construct(
        ChannelContextInterface $channelContext,
        RuleRepositoryInterface $ruleRepository,
        UrlGeneratorInterface $urlGenerator,
        RuleEligibilityCheckerInterface $ruleEligibilityChecker
    ) {
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

        $request = $event->getRequest();

        foreach ($rules as $rule) {
            if($this->ruleEligibilityChecker->isEligible($rule)) {
                try {
                    $url = $this->urlGenerator->generate($rule, $request);
                } catch (UrlGenerationException $e) {
                    continue;
                }

                $event->setResponse(new RedirectResponse($url));
                $event->stopPropagation();
                break;
            }
        }
    }
}
