<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Model;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

interface RuleInterface extends ResourceInterface, ToggleableInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function isExcludeBots(): bool;

    public function setExcludeBots(bool $excludeBots): void;

    /**
     * @return list<string>
     */
    public function getExcludedIps(): array;

    /**
     * @param list<string> $excludedIps
     */
    public function setExcludedIps(array $excludedIps): void;

    public function hasExcludedIps(): bool;

    public function getSourceChannel(): ?ChannelInterface;

    public function setSourceChannel(?ChannelInterface $sourceChannel): void;

    /**
     * @return list<string>
     */
    public function getCountryCodes(): array;

    /**
     * @param list<string> $countryCodes
     */
    public function setCountryCodes(array $countryCodes): void;

    public function getTargetChannel(): ?ChannelInterface;

    public function setTargetChannel(?ChannelInterface $targetChannel): void;

    public function getTargetLocale(): ?string;

    public function setTargetLocale(?string $targetLocale): void;
}
