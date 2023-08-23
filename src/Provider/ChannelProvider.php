<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

final class ChannelProvider implements ChannelProviderInterface
{
    use ORMManagerTrait;

    /** @var class-string<ChannelInterface> */
    private string $channelClass;

    /**
     * @param class-string<ChannelInterface> $channelClass
     */
    public function __construct(ManagerRegistry $managerRegistry, string $channelClass)
    {
        $this->managerRegistry = $managerRegistry;
        $this->channelClass = $channelClass;
    }

    public function getChannels(): array
    {
        $manager = $this->getManager($this->channelClass);

        /** @var mixed $channels */
        $channels = $manager->createQueryBuilder()
            ->select('c, l')
            ->from($this->channelClass, 'c')
            ->join('c.locales', 'l')
            ->andWhere('c.enabled = true')
            ->getQuery()
            ->getResult();

        Assert::isArray($channels);
        Assert::allIsInstanceOf($channels, ChannelInterface::class);

        return $channels;
    }
}
