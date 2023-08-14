<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGeoPlugin\EligibilityChecker;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusGeoPlugin\EligibilityChecker\CountryCodeRuleEligibilityChecker;
use Setono\SyliusGeoPlugin\Model\Rule;
use Setono\SyliusGeoPlugin\Provider\CountryCodeProviderInterface;

/**
 * @covers \Setono\SyliusGeoPlugin\EligibilityChecker\CountryCodeRuleEligibilityChecker
 */
final class CountryCodeRuleEligibilityCheckerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_is_eligible(): void
    {
        $rule = new Rule();
        $rule->setCountryCodes(['DK']);

        $countryCodeProvider = $this->prophesize(CountryCodeProviderInterface::class);
        $countryCodeProvider->getCountryCode()->willReturn('DK');

        $checker = new CountryCodeRuleEligibilityChecker($countryCodeProvider->reveal());
        self::assertTrue($checker->isEligible($rule));
    }

    /**
     * @test
     */
    public function it_is_not_eligible(): void
    {
        $rule = new Rule();
        $rule->setCountryCodes(['DK']);

        $countryCodeProvider = $this->prophesize(CountryCodeProviderInterface::class);
        $countryCodeProvider->getCountryCode()->willReturn('US');

        $checker = new CountryCodeRuleEligibilityChecker($countryCodeProvider->reveal());
        self::assertFalse($checker->isEligible($rule));
    }

    /**
     * @test
     */
    public function it_is_not_eligible_if_the_country_code_is_null(): void
    {
        $rule = new Rule();
        $rule->setCountryCodes(['DK']);

        $countryCodeProvider = $this->prophesize(CountryCodeProviderInterface::class);
        $countryCodeProvider->getCountryCode()->willReturn(null);

        $checker = new CountryCodeRuleEligibilityChecker($countryCodeProvider->reveal());
        self::assertFalse($checker->isEligible($rule));
    }
}
