<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Symfony\Component\HttpFoundation\Request;

final class Route
{
    public const ATTRIBUTE_ROUTE = '_route';

    public const ATTRIBUTE_ROUTE_PARAMETERS = '_route_params';

    private string $route;

    private array $routeParameters;

    private ?string $localeCode;

    public function __construct(string $route, array $routeParameters, string $localeCode = null)
    {
        $this->route = $route;
        $this->routeParameters = $routeParameters;
        $this->localeCode = $localeCode;
    }

    /**
     * @throws \InvalidArgumentException if the request does not have a route attribute
     */
    public static function fromRequest(Request $request): self
    {
        /** @var mixed $route */
        $route = $request->attributes->get(self::ATTRIBUTE_ROUTE);
        if (!is_string($route) || '' === $route) {
            throw new \InvalidArgumentException(sprintf(
                'The %s attribute is not set on the request',
                self::ATTRIBUTE_ROUTE
            ));
        }

        /** @var mixed $routeParameters */
        $routeParameters = $request->attributes->get(self::ATTRIBUTE_ROUTE_PARAMETERS);
        if (!is_array($routeParameters)) {
            $routeParameters = [];
        }

        return new self($route, $routeParameters);
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getRouteParameters(): array
    {
        return $this->routeParameters;
    }

    /**
     * @psalm-assert-if-true mixed $this->routeParameters[$routeParameter]
     */
    public function hasRouteParameter(string $routeParameter): bool
    {
        return array_key_exists($routeParameter, $this->routeParameters);
    }

    /**
     * @return mixed
     */
    public function getRouteParameter(string $routeParameter)
    {
        if (!$this->hasRouteParameter($routeParameter)) {
            throw new \InvalidArgumentException(sprintf('The route parameter "%s" does not exist', $routeParameter));
        }

        return $this->routeParameters[$routeParameter];
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    /**
     * You can set the locale here if you want to explicitly define a locale for the URL being generated
     */
    public function withLocaleCode(?string $localeCode): self
    {
        $new = clone $this;
        $new->localeCode = $localeCode;

        return $new;
    }
}
