<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;

final class FallbackChannelUrlGenerator extends AbstractChannelUrlGenerator
{
    public function generate(ChannelInterface $channel, string $locale = null, Request $request = null): string
    {
        $route = $this->assertRoute($request);
        $routeParameters = $this->assertRouteParameters($request);
        $routeParameters['_locale'] = $this->getTargetLocale($channel, $locale);

        return $this->generateChannelUrl($channel, $route, $routeParameters);
    }

    public function supports(ChannelInterface $channel, string $locale = null, Request $request = null): bool
    {
        $request = $this->getRequest($request);

        return $request->attributes->has(self::ATTRIBUTE_ROUTE) && $request->attributes->has(self::ATTRIBUTE_ROUTE_PARAMETERS);
    }
}
