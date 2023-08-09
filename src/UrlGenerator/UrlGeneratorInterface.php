<?php
declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\SyliusGeoPlugin\Model\RuleInterface;
use Symfony\Component\HttpFoundation\Request;

interface UrlGeneratorInterface
{
    /**
     * Must generate a URL to the rule's target channel. If it's not possible, return null
     */
    public function generate(RuleInterface $rule, Request $request): ?string;
}
