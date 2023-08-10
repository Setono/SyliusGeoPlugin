<?php
declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Setono\SyliusGeoPlugin\Model\RuleInterface;
use Symfony\Component\HttpFoundation\Request;

interface UrlGeneratorInterface
{
    /**
     * Generates a URL to the rule's target channel
     *
     * @throws UrlGenerationException if it's not possible to generate a URL
     */
    public function generate(RuleInterface $rule, Request $request): string;

    /**
     * Returns true if the url generator can generate an URL based on the given rule and request
     */
    public function supports(RuleInterface $rule, Request $request): bool;
}
