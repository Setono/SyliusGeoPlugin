<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGeoPlugin\Provider;

use PHPUnit\Framework\TestCase;
use Setono\SyliusGeoPlugin\Provider\QueryBasedCountryCodeProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @covers \Setono\SyliusGeoPlugin\Provider\QueryBasedCountryCodeProvider
 */
final class QueryBasedCountryCodeProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it_provides(): void
    {
        $request = new Request([
            '_countryCode' => 'DK',
        ]);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $provider = new QueryBasedCountryCodeProvider($requestStack);
        self::assertSame('DK', $provider->getCountryCode());
    }
}
