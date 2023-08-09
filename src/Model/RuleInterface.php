<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface RuleInterface extends ResourceInterface
{
    public function getId(): ?int;
}
