<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusGeoPlugin\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Setono\SyliusGeoPlugin\DependencyInjection\SetonoSyliusGeoExtension;

/**
 * See examples of tests and configuration options here: https://github.com/SymfonyTest/SymfonyDependencyInjectionTest
 */
final class SetonoSyliusGeoExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new SetonoSyliusGeoExtension(),
        ];
    }
}
