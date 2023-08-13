<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Exception;

final class UrlGenerationException extends \RuntimeException
{
    public static function invalidRoute(): self
    {
        return new self('The _route attribute was invalid');
    }
}
