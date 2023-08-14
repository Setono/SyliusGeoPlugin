<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SlugAwareChannelUrlGenerator extends AbstractChannelUrlGenerator
{
    private RepositoryInterface $repository;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack,
        RepositoryInterface $repository
    ) {
        parent::__construct($urlGenerator, $requestStack);

        $this->repository = $repository;
    }

    public function generate(ChannelInterface $channel, string $locale = null, Request $request = null): string
    {
        $request = $this->getRequest($request);
        $newLocale = $this->getNewLocale($channel, $locale);

        $route = $this->assertRoute($request);
        $routeParameters = $this->assertRouteParameters($request);
        $currentSlug = $this->assertRouteParameter($routeParameters, 'slug');

        $routeParameters['slug'] = $this->getNewSlug($this->repository, $currentSlug, $request->getLocale(), $newLocale);
        $routeParameters['_locale'] = $newLocale;

        try {
            $path = $this->urlGenerator->generate($route, $routeParameters);
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
        /** @var mixed $routeParameters */
        $routeParameters = $this->getRequest($request)->attributes->get(self::ATTRIBUTE_ROUTE_PARAMETERS);

        return is_array($routeParameters) && isset($routeParameters['slug']);
    }
}
