<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\CompositeCompilerPass\CompositeService;
use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends CompositeService<ChannelUrlGeneratorInterface>
 */
final class CompositeChannelUrlGenerator extends CompositeService implements ChannelUrlGeneratorInterface
{
    public function generate(ChannelInterface $channel, string $locale = null, Request $request = null): string
    {
        foreach ($this->services as $service) {
            if ($service->supports($channel, $locale, $request)) {
                try {
                    return $service->generate($channel, $locale, $request);
                } catch (UrlGenerationException $e) {
                    continue;
                }
            }
        }

        throw new UrlGenerationException('Unable to generate a URL based on the given arguments');
    }

    public function supports(ChannelInterface $channel, string $locale = null, Request $request = null): bool
    {
        foreach ($this->services as $service) {
            if ($service->supports($channel, $locale, $request)) {
                return true;
            }
        }

        return false;
    }
}
