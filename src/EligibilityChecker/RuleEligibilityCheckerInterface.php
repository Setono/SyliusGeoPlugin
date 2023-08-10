<?php
declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\EligibilityChecker;

use Setono\SyliusGeoPlugin\Model\RuleInterface;

interface RuleEligibilityCheckerInterface
{
    public function isEligible(RuleInterface $rule): bool;
}
