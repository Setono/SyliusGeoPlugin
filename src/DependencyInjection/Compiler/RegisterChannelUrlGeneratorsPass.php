<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\DependencyInjection\Compiler;

use Setono\SyliusGeoPlugin\UrlGenerator\SlugAwareChannelUrlGenerator;
use Sylius\Component\Resource\Model\SlugAwareInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterChannelUrlGeneratorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('sylius.resources')) {
            return;
        }

        /** @var array<string, array{classes: array{model: string}}> $resources */
        $resources = $container->getParameter('sylius.resources');

        foreach ($resources as $resourceAlias => $resource) {
            $class = $resource['classes']['model'];

            // if the slug isn't translated then the FallbackChannelUrlGenerator will just create the URL
            if (!is_a($class, TranslationInterface::class, true)) {
                continue;
            }

            if (!is_a($class, SlugAwareInterface::class, true)) {
                continue;
            }

            self::registerChannelUrlGenerator($resourceAlias, $container);
        }
    }

    private static function registerChannelUrlGenerator(string $resourceAlias, ContainerBuilder $container): void
    {
        /** @psalm-suppress PossiblyUndefinedArrayOffset */
        [$applicationName, $resourceName] = explode('.', $resourceAlias, 2);
        $repositoryId = sprintf('%s.repository.%s', $applicationName, $resourceName);

        if (!$container->has($repositoryId)) {
            return;
        }

        $definition = new Definition(SlugAwareChannelUrlGenerator::class, [
            new Reference('router'),
            new Reference('request_stack'),
            new Reference($repositoryId),
        ]);
        $definition->addTag('setono_sylius_geo.channel_url_generator', ['priority' => -50]);

        $container->setDefinition(
            sprintf('setono_sylius_geo.url_generator.slug_aware.%s', $resourceAlias),
            $definition
        );
    }
}
