<?php
declare(strict_types=1);


namespace Setono\SyliusGeoPlugin\Provider;


use Symfony\Component\HttpFoundation\RequestStack;

/**
 * You can test the geo redirection by appending ?_countryCode=[COUNTRY_CODE] to any URL
 */
final class QueryBasedCountryCodeProvider implements CountryCodeProviderInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getCountryCode(): ?string
    {
        $request = $this->requestStack->getMainRequest();
        if(null === $request) {
            return null;
        }

        $countryCode = $request->query->get('_countryCode') ?? $request->query->get('_country_code');

        return is_string($countryCode) && '' !== $countryCode ? $countryCode : null;
    }
}
