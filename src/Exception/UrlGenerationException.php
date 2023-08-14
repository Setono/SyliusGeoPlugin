<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Exception;

use Setono\SyliusGeoPlugin\UrlGenerator\AbstractChannelUrlGenerator;

final class UrlGenerationException extends \RuntimeException
{
    public static function invalidRoute(): self
    {
        return new self(sprintf('The "%s" attribute was invalid', AbstractChannelUrlGenerator::ATTRIBUTE_ROUTE));
    }

    public static function invalidRouteParameters(): self
    {
        return new self(sprintf('The "%s" attribute was invalid', AbstractChannelUrlGenerator::ATTRIBUTE_ROUTE_PARAMETERS));
    }

    public static function missingOrInvalidRouteParameters(string ...$missingParameters): self
    {
        return new self(sprintf(
            'The %s attribute had missing or invalid parameters: [%s]',
            AbstractChannelUrlGenerator::ATTRIBUTE_ROUTE_PARAMETERS,
            implode(', ', $missingParameters)
        ));
    }
}
