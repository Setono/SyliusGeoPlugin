<?php
declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\EligibilityChecker;

use Setono\BotDetectionBundle\BotDetector\BotDetectorInterface;
use Setono\SyliusGeoPlugin\Model\RuleInterface;

final class BotRuleEligibilityChecker implements RuleEligibilityCheckerInterface
{
    private BotDetectorInterface $botDetector;

    public function __construct(BotDetectorInterface $botDetector)
    {
        $this->botDetector = $botDetector;
    }

    public function isEligible(RuleInterface $rule): bool
    {
        if(!$rule->isExcludeBots()) {
            return true;
        }

        return !$this->botDetector->isBotRequest();
    }
}
