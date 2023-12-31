<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Model;

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

    protected ?string $targetLocale = null;

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
        $this->excludedIps = array_values(array_unique($excludedIps));
    }

    public function hasExcludedIps(): bool
    {
        return [] !== $this->excludedIps;
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
        $this->countryCodes = array_values(array_unique($countryCodes));
    }

    public function getTargetChannel(): ?ChannelInterface
    {
        return $this->targetChannel;
    }

    public function setTargetChannel(?ChannelInterface $targetChannel): void
    {
        $this->targetChannel = $targetChannel;
    }

    public function getTargetLocale(): ?string
    {
        return $this->targetLocale;
    }

    public function setTargetLocale(?string $targetLocale): void
    {
        $this->targetLocale = $targetLocale;
    }
}
