<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

interface RuleInterface extends ResourceInterface, ToggleableInterface
{
    public function getId(): ?int;
}
