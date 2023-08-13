<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Provider;

interface CountryCodeProviderInterface
{
    /**
     * Will return the requestees country code if possible, else it returns null
     */
    public function getCountryCode(): ?string;
}
