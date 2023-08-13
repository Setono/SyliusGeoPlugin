<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\EligibilityChecker;

use Setono\CompositeCompilerPass\CompositeService;
use Setono\SyliusGeoPlugin\Model\RuleInterface;

/**
 * @extends CompositeService<RuleEligibilityCheckerInterface>
 */
final class CompositeRuleEligibilityChecker extends CompositeService implements RuleEligibilityCheckerInterface
{
    public function isEligible(RuleInterface $rule): bool
    {
        foreach ($this->services as $service) {
            if (!$service->isEligible($rule)) {
                return false;
            }
        }

        return true;
    }
}
