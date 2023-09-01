<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractChannelUrlGenerator implements ChannelUrlGeneratorInterface
{
    public const ATTRIBUTE_ROUTE = '_route';

    public const ATTRIBUTE_ROUTE_PARAMETERS = '_route_params';

    protected UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    protected function doGenerate(string $route, array $parameters): string
    {
        try {
            return $this->urlGenerator->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
        } catch (\Throwable $e) {
            throw new UrlGenerationException(
                sprintf('An error occurrd when trying to generate a URL from route "%s": %s', $route, $e->getMessage()),
                0,
                $e
            );
        }
    }

    /**
     * @param string|null $locale if null, the method will try to get the locale from the given $channel
     *
     * @throws UrlGenerationException if it's not possible to retrieve a locale from the given $channel
     */
    protected function resolveTargetLocale(BaseChannelInterface $channel, string $locale = null): string
    {
        if (null !== $locale) {
            return $locale;
        }

        if (!$channel instanceof ChannelInterface) {
            throw new UrlGenerationException(sprintf('The $channel is not an instance of %s', ChannelInterface::class));
        }

        $defaultLocale = $channel->getDefaultLocale();
        if (null === $defaultLocale) {
            throw new UrlGenerationException(sprintf(
                'The default locale on the channel "%s" is null',
                (string) $channel->getCode()
            ));
        }

        $locale = $defaultLocale->getCode();
        if (null === $locale) {
            throw new UrlGenerationException(sprintf(
                'The locale code is null on the default locale on the channel "%s"',
                (string) $channel->getCode()
            ));
        }

        return $locale;
    }
}
