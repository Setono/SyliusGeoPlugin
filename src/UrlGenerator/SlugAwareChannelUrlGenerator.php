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
    private RepositoryInterface $repository;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack,
        RepositoryInterface $repository
    ) {
        parent::__construct($urlGenerator, $requestStack);

        $this->repository = $repository;
    }

    public function generate(ChannelInterface $channel, string $locale = null, Request $request = null): string
    {
        $request = $this->getRequest($request);
        $targetLocale = $this->resolveTargetLocale($channel, $locale);

        $route = $this->ensureRoute($request);
        $routeParameters = $this->ensureRouteParameters($request);
        $currentSlug = $this->ensureRouteParameter($routeParameters, 'slug');

        $routeParameters['slug'] = $this->resolveTargetSlug($this->repository, $currentSlug, $request->getLocale(), $targetLocale);
        $routeParameters['_locale'] = $targetLocale;

        return $this->urlGenerator->generate($route, $routeParameters);
    }

    public function supports(ChannelInterface $channel, string $locale = null, Request $request = null): bool
    {
        /** @var mixed $routeParameters */
        $routeParameters = $this->getRequest($request)->attributes->get(self::ATTRIBUTE_ROUTE_PARAMETERS);

        return is_array($routeParameters) && isset($routeParameters['slug']);
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
}
