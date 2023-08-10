<?php
declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\EligibilityChecker;

use Setono\BotDetectionBundle\BotDetector\BotDetectorInterface;
use Setono\SyliusGeoPlugin\Model\RuleInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class IpRuleEligibilityChecker implements RuleEligibilityCheckerInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function isEligible(RuleInterface $rule): bool
    {
        if(!$rule->hasExcludedIps()) {
            return true;
        }

        $request = $this->requestStack->getMainRequest();
        if(null === $request) {
            return true;
        }

        $ip = $request->getClientIp();
        if(null === $ip) {
            return true;
        }

        return !in_array($ip, $rule->getExcludedIps(), true);
    }
}
