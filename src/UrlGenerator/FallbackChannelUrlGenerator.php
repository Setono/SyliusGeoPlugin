<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;

final class FallbackChannelUrlGenerator extends AbstractChannelUrlGenerator
{
    public function generate(ChannelInterface $channel, string $locale = null, Request $request = null): string
    {
        $route = $this->assertRoute($request);
        $routeParameters = $this->assertRouteParameters($request);

        try {
            $url = $this->urlGenerator->generate($route, $routeParameters);
        } catch (\Throwable $e) {
            throw new UrlGenerationException(sprintf('An error occurred trying to generate the URL "%s": %s', $route, $e->getMessage()), 0, $e);
        }

        $hostname = $channel->getHostname();
        if (null === $hostname) {
            throw new UrlGenerationException('The hostname on the target channel was null');
        }

        return sprintf('https://%s%s', $hostname, $url);
    }

    public function supports(ChannelInterface $channel, string $locale = null, Request $request = null): bool
    {
        $request = $this->getRequest($request);

        return $request->attributes->has('_route') && $request->attributes->has('_route_params');
    }
}
