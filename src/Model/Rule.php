<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Model;

// todo
// 1. Add bool to match bots
// 2. Add bool to redirect to frontpage if a URL cannot be produced
// 3. Add list of excluded IPs
use Sylius\Component\Resource\Model\ToggleableTrait;

class Rule implements RuleInterface
{
    use ToggleableTrait;

    protected ?int $id = null;

    protected ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
