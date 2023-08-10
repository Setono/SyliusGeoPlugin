<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin;

use Setono\CompositeCompilerPass\CompositeCompilerPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SetonoSyliusGeoPlugin extends AbstractResourceBundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CompositeCompilerPass(
            'setono_sylius_geo.provider.country_code.composite',
            'setono_sylius_geo.country_code_provider'
        ));

        $container->addCompilerPass(new CompositeCompilerPass(
            'setono_sylius_geo.eligibility_checker.rule.composite',
            'setono_sylius_geo.rule_eligibility_checker'
        ));


    }

    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }
}
