<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProductChannelUrlGenerator extends AbstractChannelUrlGenerator
{
    private const ROUTE = 'sylius_shop_product_show';

    private RepositoryInterface $productTranslationRepository;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack,
        RepositoryInterface $productTranslationRepository
    ) {
        parent::__construct($urlGenerator, $requestStack);

        $this->productTranslationRepository = $productTranslationRepository;
    }

    public function generate(ChannelInterface $channel, string $locale = null, Request $request = null): string
    {
        $request = $this->getRequest($request);
        $newLocale = $this->getNewLocale($channel, $locale);

        $routeParameters = $this->assertRouteParameters($request);
        $currentSlug = $this->assertRouteParameter($routeParameters, 'slug');

        $newSlug = $this->getNewSlug($this->productTranslationRepository, $currentSlug, $request->getLocale(), $newLocale);

        try {
            $url = $this->urlGenerator->generate(self::ROUTE, [
                'slug' => $newSlug,
                'locale' => $newLocale,
            ]);
        } catch (\Throwable $e) {
            throw new UrlGenerationException(sprintf(
                'An error occurred trying to generate the URL: %s',
                $e->getMessage()
            ), 0, $e);
        }

        $hostname = $channel->getHostname();
        if (null === $hostname) {
            throw new UrlGenerationException('The hostname on the target channel was null');
        }

        return sprintf('https://%s%s', $hostname, $url);
    }

    public function supports(ChannelInterface $channel, string $locale = null, Request $request = null): bool
    {
        return $this->getRequest($request)->attributes->get(self::ATTRIBUTE_ROUTE) === self::ROUTE;
    }
}
