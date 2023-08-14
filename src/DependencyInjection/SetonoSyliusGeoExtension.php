<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\DependencyInjection;

use Setono\SyliusGeoPlugin\EligibilityChecker\RuleEligibilityCheckerInterface;
use Setono\SyliusGeoPlugin\Provider\CountryCodeProviderInterface;
use Setono\SyliusGeoPlugin\UrlGenerator\ChannelUrlGeneratorInterface;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusGeoExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         *
         * @var array{resources: array<string, mixed>} $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');

        $this->registerResources(
            'setono_sylius_geo',
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            $config['resources'],
            $container,
        );

        $container->registerForAutoconfiguration(ChannelUrlGeneratorInterface::class)
            ->addTag('setono_sylius_geo.channel_url_generator');

        $container->registerForAutoconfiguration(CountryCodeProviderInterface::class)
            ->addTag('setono_sylius_geo.country_code_provider');

        $container->registerForAutoconfiguration(RuleEligibilityCheckerInterface::class)
            ->addTag('setono_sylius_geo.rule_eligibility_checker');
    }
}
