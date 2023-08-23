<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

abstract class AbstractChannelUrlGenerator implements ChannelUrlGeneratorInterface
{
    public const ATTRIBUTE_ROUTE = '_route';

    public const ATTRIBUTE_ROUTE_PARAMETERS = '_route_params';

    protected UrlGeneratorInterface $urlGenerator;

    private RequestStack $requestStack;

    public function __construct(UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
    {
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * @param Request|null $request if the $request is null, the method will try to get the request from the request stack
     *
     * @throws UrlGenerationException if the request stack does not have a main request
     */
    protected function getRequest(Request $request = null): Request
    {
        if (null !== $request) {
            return $request;
        }

        $request = $this->requestStack->getMainRequest();
        if (null === $request) {
            throw new UrlGenerationException('The URL generator can only be used in a request cycle');
        }

        return $request;
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

    protected function ensureRoute(Request $request = null): string
    {
        $request = $this->getRequest($request);
        $route = $request->attributes->get(self::ATTRIBUTE_ROUTE);
        if (!is_string($route) || '' === $route) {
            throw UrlGenerationException::invalidRoute();
        }

        return $route;
    }

    protected function ensureRouteParameters(Request $request = null): array
    {
        $request = $this->getRequest($request);

        /** @var mixed $routeParameters */
        $routeParameters = $request->attributes->get(self::ATTRIBUTE_ROUTE_PARAMETERS);
        if (!is_array($routeParameters)) {
            throw UrlGenerationException::invalidRouteParameters();
        }

        return $routeParameters;
    }

    /**
     * @psalm-assert non-empty-string $routeParameters[$parameter]
     */
    protected function ensureRouteParameter(array $routeParameters, string $parameter): string
    {
        if (!isset($routeParameters[$parameter]) || !is_string($routeParameters[$parameter]) || '' === $routeParameters[$parameter]) {
            throw UrlGenerationException::missingOrInvalidRouteParameters($parameter);
        }

        return $routeParameters[$parameter];
    }
}
