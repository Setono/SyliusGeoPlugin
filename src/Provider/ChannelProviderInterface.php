<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Provider;

use Sylius\Component\Core\Model\ChannelInterface;

interface ChannelProviderInterface
{
    /**
     * @return array<array-key, ChannelInterface>
     */
    public function getChannels(): array;
}
