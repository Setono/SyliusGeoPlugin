<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\CompositeCompilerPass\CompositeService;
use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * @extends CompositeService<ChannelUrlGeneratorInterface>
 */
final class CompositeChannelUrlGenerator extends CompositeService implements ChannelUrlGeneratorInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function generate(ChannelInterface $channel, string $locale = null, Request $request = null): string
    {
        if ([] === $this->services) {
            throw new UrlGenerationException('No channel URL generators has been registered');
        }

        $hostname = $channel->getHostname();
        if (null === $hostname) {
            throw new UrlGenerationException(sprintf(
                'The hostname on the channel "%s" was null',
                (string) $channel->getCode()
            ));
        }

        $oldRequestContext = $this->urlGenerator->getContext();

        $this->urlGenerator->setContext(RequestContext::fromUri(sprintf('https://%s', $hostname)));

        $hasSupportingChannelUrlGenerator = false;
        $lastException = null;

        foreach ($this->services as $service) {
            if ($service->supports($channel, $locale, $request)) {
                $hasSupportingChannelUrlGenerator = true;

                try {
                    $url = $service->generate($channel, $locale, $request);
                    $this->urlGenerator->setContext($oldRequestContext);

                    return $url;
                } catch (UrlGenerationException $e) {
                    $lastException = $e;

                    continue;
                }
            }
        }

        $this->urlGenerator->setContext($oldRequestContext);

        if (!$hasSupportingChannelUrlGenerator) {
            throw new UrlGenerationException('None of the registered channel url generators supports the given arguments');
        }

        throw new UrlGenerationException(sprintf('Unable to generate a URL based on the given arguments%s', null === $lastException ? '' : '. Last error was: ' . $lastException->getMessage()));
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
