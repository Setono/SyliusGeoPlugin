<?php
declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\SyliusGeoPlugin\Model\RuleInterface;
use Symfony\Component\HttpFoundation\Request;

final class FallbackUrlGenerator implements UrlGeneratorInterface
{
    public function generate(RuleInterface $rule, Request $request): ?string
    {
        return 'https://www.google.com/'; // todo
    }
}
