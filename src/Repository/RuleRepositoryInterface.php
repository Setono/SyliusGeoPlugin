<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Repository;

use Setono\SyliusGeoPlugin\Model\RuleInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<RuleInterface>
 */
interface RuleRepositoryInterface extends RepositoryInterface
{
    /**
     * @return list<RuleInterface>
     */
    public function findEnabledBySourceChannel(ChannelInterface $channel): array;
}
