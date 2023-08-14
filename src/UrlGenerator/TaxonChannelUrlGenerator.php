<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class TaxonChannelUrlGenerator extends AbstractChannelUrlGenerator
{
    private const ROUTE = 'sylius_shop_product_index';

    private RepositoryInterface $taxonTranslationRepository;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack,
        RepositoryInterface $taxonTranslationRepository
    ) {
        parent::__construct($urlGenerator, $requestStack);

        $this->taxonTranslationRepository = $taxonTranslationRepository;
    }

    public function generate(ChannelInterface $channel, string $locale = null, Request $request = null): string
    {
        $request = $this->getRequest($request);
        $newLocale = $this->getNewLocale($channel, $locale);

        $routeParameters = $this->assertRouteParameters($request);
        $currentSlug = $this->assertRouteParameter($routeParameters, 'slug');

        $newSlug = $this->getNewSlug($this->taxonTranslationRepository, $currentSlug, $request->getLocale(), $newLocale);

        try {
            $path = $this->urlGenerator->generate(self::ROUTE, [
                'slug' => $newSlug,
                'locale' => $newLocale,
            ]);
        } catch (\Throwable $e) {
            throw new UrlGenerationException(sprintf(
                'An error occurred trying to generate the URL: %s',
                $e->getMessage()
            ), 0, $e);
        }

        return $this->getChannelUrl($channel, $path);
    }

    public function supports(ChannelInterface $channel, string $locale = null, Request $request = null): bool
    {
        return $this->getRequest($request)->attributes->get(self::ATTRIBUTE_ROUTE) === self::ROUTE;
    }
}
