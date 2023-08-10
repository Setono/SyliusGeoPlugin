<?php
declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\EligibilityChecker;

use Setono\BotDetectionBundle\BotDetector\BotDetectorInterface;
use Setono\SyliusGeoPlugin\Model\RuleInterface;
use Setono\SyliusGeoPlugin\Provider\CountryCodeProviderInterface;

final class CountryCodeRuleEligibilityChecker implements RuleEligibilityCheckerInterface
{
    private CountryCodeProviderInterface $countryCodeProvider;

    public function __construct(CountryCodeProviderInterface $countryCodeProvider)
    {
        $this->countryCodeProvider = $countryCodeProvider;
    }

    public function isEligible(RuleInterface $rule): bool
    {
        $countryCode = $this->countryCodeProvider->getCountryCode();
        if(null === $countryCode) {
            return false;
        }

        return in_array($countryCode, $rule->getCountryCodes(), true);
    }
}
