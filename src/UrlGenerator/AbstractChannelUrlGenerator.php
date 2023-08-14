<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\SlugAwareInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
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

    protected function getNewLocale(BaseChannelInterface $channel, string $locale = null): string
    {
        if (null !== $locale) {
            return $locale;
        }

        if (!$channel instanceof ChannelInterface) {
            throw new UrlGenerationException(sprintf('The $channel is not an instance of %s', ChannelInterface::class));
        }

        $defaultLocale = $channel->getDefaultLocale();
        if (null === $defaultLocale) {
            throw new UrlGenerationException(sprintf('The default locale on the channel "%s" is null', (string) $channel->getCode()));
        }

        $locale = $defaultLocale->getCode();
        if (null === $locale) {
            throw new UrlGenerationException(sprintf('The locale code is null on the default locale on the channel "%s"', (string) $channel->getCode()));
        }

        return $locale;
    }

    // todo should we make this method better?
    protected function getNewSlug(RepositoryInterface $repository, string $currentSlug, string $currentLocale, string $newLocale): string
    {
        try {
            /** @var TranslationInterface|null $currentTranslated */
            $currentTranslated = $repository->findOneBy([
                'locale' => $currentLocale,
                'slug' => $currentSlug,
            ]);
            Assert::isInstanceOf($currentTranslated, TranslationInterface::class);

            /** @var SlugAwareInterface|null $newTranslated */
            $newTranslated = $repository->findOneBy([
                'translatable' => $currentTranslated->getTranslatable(),
                'locale' => $newLocale,
            ]);
            Assert::isInstanceOf($newTranslated, SlugAwareInterface::class);

            $slug = $newTranslated->getSlug();
            Assert::stringNotEmpty($slug);

            return $slug;
        } catch (\Throwable $e) {
            throw new UrlGenerationException(sprintf(
                'An error occurred when trying to get a new slug from the following arguments: Current slug: %s, current locale: %s, new locale: %s. The error was: %s',
                $currentSlug,
                $currentLocale,
                $newLocale,
                $e->getMessage()
            ), 0, $e);
        }
    }

    protected function generateChannelUrl(BaseChannelInterface $channel, string $route, array $routeParameters): string
    {
        try {
            $path = $this->urlGenerator->generate($route, $routeParameters);
        } catch (\Throwable $e) {
            throw new UrlGenerationException(sprintf(
                'An error occurred trying to generate the URL "%s": %s',
                $route,
                $e->getMessage()
            ), 0, $e);
        }

        $hostname = $channel->getHostname();
        if (null === $hostname) {
            throw new UrlGenerationException(sprintf('The hostname on the channel "%s" was null', (string) $channel->getCode()));
        }

        return sprintf('https://%s%s', $hostname, $path);
    }

    protected function assertRoute(Request $request = null): string
    {
        $request = $this->getRequest($request);
        $route = $request->attributes->get(self::ATTRIBUTE_ROUTE);
        if (!is_string($route) || '' === $route) {
            throw UrlGenerationException::invalidRoute();
        }

        return $route;
    }

    protected function assertRouteParameters(Request $request = null): array
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
    protected function assertRouteParameter(array $routeParameters, string $parameter): string
    {
        if (!isset($routeParameters[$parameter]) || !is_string($routeParameters[$parameter]) || '' === $routeParameters[$parameter]) {
            throw UrlGenerationException::missingOrInvalidRouteParameters($parameter);
        }

        return $routeParameters[$parameter];
    }
}
