<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\UrlGenerator;

use Setono\SyliusGeoPlugin\Exception\UrlGenerationException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * The purpose of a Channel URL Generator is to generate a URL to a channel based on the values of the incoming request.
 *
 * An example: The visitor is browsing example.com/product/black-t-shirt, and you want to create a URL to the German
 * counterpart and generate a URL like example.de/produkte/schwarz-t-shirt
 */
interface ChannelUrlGeneratorInterface
{
    /**
     * Generates a URL from the given route on the given channel
     *
     * @throws UrlGenerationException if it's not possible to generate a URL
     */
    public function generate(ChannelInterface $channel, Route $route): string;

    /**
     * Returns true if the url generator can generate a URL based on the given arguments
     */
    public function supports(ChannelInterface $channel, Route $route): bool;
}
