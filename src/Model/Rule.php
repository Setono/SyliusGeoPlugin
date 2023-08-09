<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Model;

// todo
// 2. Add bool to redirect to frontpage if a URL cannot be produced
// 3. Add list of excluded IPs
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;

class Rule implements RuleInterface
{
    use ToggleableTrait;

    protected ?int $id = null;

    protected ?string $name = null;

    protected bool $excludeBots = true;

    /** @var list<string> */
    protected array $excludedIps = [];

    protected ?ChannelInterface $sourceChannel = null;

    /** @var list<string> */
    protected array $countryCodes = [];

    protected ?ChannelInterface $targetChannel = null;

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

    public function isExcludeBots(): bool
    {
        return $this->excludeBots;
    }

    public function setExcludeBots(bool $excludeBots): void
    {
        $this->excludeBots = $excludeBots;
    }

    public function getExcludedIps(): array
    {
        return $this->excludedIps;
    }

    public function setExcludedIps(array $excludedIps): void
    {
        $this->excludedIps = $excludedIps;
    }

    public function getSourceChannel(): ?ChannelInterface
    {
        return $this->sourceChannel;
    }

    public function setSourceChannel(?ChannelInterface $sourceChannel): void
    {
        $this->sourceChannel = $sourceChannel;
    }

    public function getCountryCodes(): array
    {
        return $this->countryCodes;
    }

    public function setCountryCodes(array $countryCodes): void
    {
        $this->countryCodes = $countryCodes;
    }

    public function getTargetChannel(): ?ChannelInterface
    {
        return $this->targetChannel;
    }

    public function setTargetChannel(?ChannelInterface $targetChannel): void
    {
        $this->targetChannel = $targetChannel;
    }
}
