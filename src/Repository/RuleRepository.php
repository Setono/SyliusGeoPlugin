<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Repository;

use Setono\SyliusGeoPlugin\Model\RuleInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Webmozart\Assert\Assert;

class RuleRepository extends EntityRepository implements RuleRepositoryInterface
{
    public function findEnabledBySourceChannel(ChannelInterface $channel): array
    {
        $objs = $this->createQueryBuilder('o')
            ->andWhere('o.enabled = true')
            ->andWhere('o.sourceChannel = :channel')
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult()
        ;

        Assert::isList($objs);
        Assert::allIsInstanceOf($objs, RuleInterface::class);

        return $objs;
    }
}
