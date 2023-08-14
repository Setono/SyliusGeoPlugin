<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGeoPlugin\EligibilityChecker;

use PHPUnit\Framework\TestCase;
use Setono\SyliusGeoPlugin\EligibilityChecker\IpRuleEligibilityChecker;
use Setono\SyliusGeoPlugin\Model\Rule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @covers \Setono\SyliusGeoPlugin\EligibilityChecker\IpRuleEligibilityChecker
 */
final class IpRuleEligibilityCheckerTest extends TestCase
{
    /**
     * @test
     */
    public function it_is_eligible(): void
    {
        $rule = new Rule();
        $rule->setExcludedIps(['123.456.789.123']);

        $checker = new IpRuleEligibilityChecker(self::getRequestStack());
        self::assertTrue($checker->isEligible($rule));
    }

    /**
     * @test
     */
    public function it_is_eligible_if_no_excluded_ips_are_present(): void
    {
        $rule = new Rule();

        $checker = new IpRuleEligibilityChecker(self::getRequestStack());
        self::assertTrue($checker->isEligible($rule));
    }

    /**
     * @test
     */
    public function it_is_eligible_if_the_request_stack_is_empty(): void
    {
        $rule = new Rule();

        $checker = new IpRuleEligibilityChecker(new RequestStack());
        self::assertTrue($checker->isEligible($rule));
    }

    /**
     * @test
     */
    public function it_is_eligible_if_the_ip_is_null(): void
    {
        $rule = new Rule();

        $checker = new IpRuleEligibilityChecker(self::getRequestStack(null));
        self::assertTrue($checker->isEligible($rule));
    }

    /**
     * @test
     */
    public function it_is_not_eligible(): void
    {
        $rule = new Rule();
        $rule->setExcludedIps(['123.123.123.123']);

        $checker = new IpRuleEligibilityChecker(self::getRequestStack());
        self::assertFalse($checker->isEligible($rule));
    }

    private static function getRequestStack(?string $ip = '123.123.123.123'): RequestStack
    {
        $server = [];
        if (null !== $ip) {
            $server['REMOTE_ADDR'] = $ip;
        }

        $request = new Request([], [], [], [], [], $server);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        return $requestStack;
    }
}
