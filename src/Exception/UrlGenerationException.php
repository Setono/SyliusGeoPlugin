<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Exception;

final class UrlGenerationException extends \RuntimeException
{
    public static function invalidRouteParameter(string $routeParameter): self
    {
        return new self(sprintf('The route parameter "%s" attribute was invalid', $routeParameter));
    }
}
