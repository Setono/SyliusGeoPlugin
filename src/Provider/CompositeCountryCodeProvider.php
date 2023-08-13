<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Provider;

use Setono\CompositeCompilerPass\CompositeService;

/**
 * @extends CompositeService<CountryCodeProviderInterface>
 */
final class CompositeCountryCodeProvider extends CompositeService implements CountryCodeProviderInterface
{
    public function getCountryCode(): ?string
    {
        foreach ($this->services as $service) {
            $countryCode = $service->getCountryCode();
            if (null !== $countryCode) {
                return $countryCode;
            }
        }

        return null;
    }
}
