<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class Extension extends AbstractExtension
{
    /**
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('ssg_hreflang_tags', [Runtime::class, 'hreflangTags'], ['is_safe' => ['html']]),
        ];
    }
}
