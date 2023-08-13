<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Provider;

use Symfony\Component\HttpFoundation\RequestStack;

final class HeaderBasedCountryCodeProvider implements CountryCodeProviderInterface
{
    private RequestStack $requestStack;

    private string $headerName;

    public function __construct(RequestStack $requestStack, string $headerName = 'X-Country')
    {
        $this->requestStack = $requestStack;
        $this->headerName = $headerName;
    }

    public function getCountryCode(): ?string
    {
        $request = $this->requestStack->getMainRequest();
        if (null === $request) {
            return null;
        }

        $countryCode = $request->headers->get($this->headerName);

        return is_string($countryCode) && '' !== $countryCode ? $countryCode : null;
    }
}
