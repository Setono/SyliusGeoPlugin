<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Model\SlugAwareInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class SlugAwareChannelUrlGenerator extends AbstractChannelUrlGenerator
{
    private RequestStack $requestStack;

    private RepositoryInterface $repository;

    private string $route;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack,
        RepositoryInterface $repository,
        string $route
    ) {
        parent::__construct($urlGenerator);

        $this->requestStack = $requestStack;
        $this->repository = $repository;
        $this->route = $route;
    }

    public function generate(ChannelInterface $channel, Route $route): string
    {
        $request = $this->getRequest();
        $targetLocale = $this->resolveTargetLocale($channel, $route->getLocaleCode());

        $currentSlug = $route->getRouteParameter('slug');
        if (!is_string($currentSlug)) {
            throw UrlGenerationException::invalidRouteParameter('slug');
        }

        $routeParameters = $route->getRouteParameters();
        $routeParameters['slug'] = $this->resolveTargetSlug($this->repository, $currentSlug, $request->getLocale(), $targetLocale);
        $routeParameters['_locale'] = $targetLocale;

        return $this->doGenerate($route->getRoute(), $routeParameters);
    }

    public function supports(ChannelInterface $channel, Route $route): bool
    {
        return $route->getRoute() === $this->route && $route->hasRouteParameter('slug');
    }

    // todo should we make this method better?
    private function resolveTargetSlug(
        RepositoryInterface $repository,
        string $currentSlug,
        string $currentLocale,
        string $newLocale
    ): string {
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

    /**
     * @throws UrlGenerationException if the request stack does not have a main request
     */
    private function getRequest(): Request
    {
        $request = $this->requestStack->getMainRequest();
        if (null === $request) {
            throw new UrlGenerationException('The URL generator can only be used in a request cycle');
        }

        return $request;
    }
}
