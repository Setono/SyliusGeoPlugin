<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGeoPlugin\Provider;

use PHPUnit\Framework\TestCase;
use Setono\SyliusGeoPlugin\Provider\HeaderBasedCountryCodeProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @covers \Setono\SyliusGeoPlugin\Provider\HeaderBasedCountryCodeProvider
 */
final class HeaderBasedCountryCodeProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it_provides(): void
    {
        $request = new Request([], [], [], [], [], [
            'HTTP_X_COUNTRY' => 'DK',
        ]);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $provider = new HeaderBasedCountryCodeProvider($requestStack, 'X-Country');
        self::assertSame('DK', $provider->getCountryCode());
    }
}
