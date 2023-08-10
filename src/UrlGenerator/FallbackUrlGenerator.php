<?php
declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Setono\SyliusGeoPlugin\Model\RuleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

final class FallbackUrlGenerator implements UrlGeneratorInterface
{
    private SymfonyUrlGeneratorInterface $symfonyUrlGenerator;

    public function __construct(SymfonyUrlGeneratorInterface $symfonyUrlGenerator)
    {
        $this->symfonyUrlGenerator = $symfonyUrlGenerator;
    }

    public function generate(RuleInterface $rule, Request $request): string
    {
        /** @var mixed $routeParameters */
        $route = $request->attributes->get('_route');
        if(!is_string($route) || '' === $route) {
            throw UrlGenerationException::invalidRoute();
        }

        /** @var mixed $routeParameters */
        $routeParameters = $request->attributes->get('_route_params');
        if(!is_array($routeParameters)) {
            $routeParameters = [];
        }

        try {
            $url = $this->symfonyUrlGenerator->generate($route, $routeParameters);
        } catch (\Throwable $e) {
            throw new UrlGenerationException(sprintf('An error occurred trying to generate the URL: %s', $e->getMessage()), 0, $e);
        }

        $targetChannel = $rule->getTargetChannel();
        if(null === $targetChannel) {
            throw new UrlGenerationException('The target channel on the rule was null');
        }

        $hostname = $targetChannel->getHostname();
        if(null === $hostname) {
            throw new UrlGenerationException('The hostname on the target channel was null');
        }

        return sprintf('https://%s%s', $hostname, $url);
    }

    public function supports(RuleInterface $rule, Request $request): bool
    {
        return $request->attributes->has('_route') && $request->attributes->has('_route_params');
    }
}
