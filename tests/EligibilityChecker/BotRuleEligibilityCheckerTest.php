<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGeoPlugin\EligibilityChecker;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\BotDetectionBundle\BotDetector\BotDetectorInterface;
use Setono\SyliusGeoPlugin\EligibilityChecker\BotRuleEligibilityChecker;
use Setono\SyliusGeoPlugin\Model\Rule;

/**
 * @covers \Setono\SyliusGeoPlugin\EligibilityChecker\BotRuleEligibilityChecker
 */
final class BotRuleEligibilityCheckerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_is_eligible(): void
    {
        $rule = new Rule();

        $botDetector = $this->prophesize(BotDetectorInterface::class);
        $botDetector->isBotRequest()->willReturn(false)->shouldBeCalled();

        $checker = new BotRuleEligibilityChecker($botDetector->reveal());
        self::assertTrue($checker->isEligible($rule));
    }

    /**
     * @test
     */
    public function it_is_eligible_if_the_flag_is_not_set(): void
    {
        $rule = new Rule();
        $rule->setExcludeBots(false);

        $botDetector = $this->prophesize(BotDetectorInterface::class);
        $botDetector->isBotRequest()->shouldNotBeCalled();

        $checker = new BotRuleEligibilityChecker($botDetector->reveal());
        self::assertTrue($checker->isEligible($rule));
    }

    /**
     * @test
     */
    public function it_is_not_eligible(): void
    {
        $rule = new Rule();

        $botDetector = $this->prophesize(BotDetectorInterface::class);
        $botDetector->isBotRequest()->willReturn(true)->shouldBeCalled();

        $checker = new BotRuleEligibilityChecker($botDetector->reveal());
        self::assertFalse($checker->isEligible($rule));
    }
}
