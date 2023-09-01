<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Sylius\Component\Channel\Model\ChannelInterface;

final class FallbackChannelUrlGenerator extends AbstractChannelUrlGenerator
{
    public function generate(ChannelInterface $channel, Route $route): string
    {
        $routeParameters = $route->getRouteParameters();
        $routeParameters['_locale'] = $this->resolveTargetLocale($channel, $route->getLocaleCode());

        return $this->doGenerate($route->getRoute(), $routeParameters);
    }

    public function supports(ChannelInterface $channel, Route $route): bool
    {
        return true;
    }
}
