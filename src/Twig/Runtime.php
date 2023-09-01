<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Twig;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Setono\SyliusGeoPlugin\Provider\ChannelProviderInterface;
use Setono\SyliusGeoPlugin\UrlGenerator\ChannelUrlGeneratorInterface;
use Setono\SyliusGeoPlugin\UrlGenerator\Route;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

final class Runtime implements RuntimeExtensionInterface, LoggerAwareInterface
{
    private LoggerInterface $logger;

    private ChannelUrlGeneratorInterface $channelUrlGenerator;

    private LocaleContextInterface $localeContext;

    private ChannelProviderInterface $channelProvider;

    private RequestStack $requestStack;

    public function __construct(
        ChannelUrlGeneratorInterface $channelUrlGenerator,
        LocaleContextInterface $localeContext,
        ChannelProviderInterface $channelProvider,
        RequestStack $requestStack
    ) {
        $this->logger = new NullLogger();
        $this->channelUrlGenerator = $channelUrlGenerator;
        $this->localeContext = $localeContext;
        $this->channelProvider = $channelProvider;
        $this->requestStack = $requestStack;
    }

    /**
     * Returns an HTML string with hreflang link tags
     */
    public function hreflangTags(): string
    {
        $request = $this->requestStack->getMainRequest();
        if (null === $request) {
            return '';
        }

        try {
            $route = Route::fromRequest($request);
        } catch (\InvalidArgumentException $e) {
            return '';
        }

        try {
            $currentLocaleCode = $this->localeContext->getLocaleCode();

            /** @var list<string> $links */
            $links = [];

            foreach ($this->channelProvider->getChannels() as $channel) {
                foreach ($channel->getLocales() as $locale) {
                    $localeCode = $locale->getCode();

                    // we don't want to output hreflang tags for the same locale as we are currently browsing
                    if (null === $localeCode || $localeCode === $currentLocaleCode) {
                        continue;
                    }

                    try {
                        $links[] = sprintf(
                            '<link rel="alternate" hreflang="%s" href="%s">',
                            str_replace('_', '-', $localeCode),
                            $this->channelUrlGenerator->generate($channel, $route)
                        );
                    } catch (UrlGenerationException $e) {
                        $this->logger->error($e->getMessage());
                    }
                }
            }

            return implode("\n", array_unique($links));
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }

        return '';
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
