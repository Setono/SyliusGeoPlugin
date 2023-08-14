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
     * Generates a URL to the rule's target channel
     *
     * @param string|null $locale if the $locale is null, the default locale from the channel will be used
     * @param Request|null $request if the $request is null, the main request from the request stack will be used
     *
     * @throws UrlGenerationException if it's not possible to generate a URL
     */
    public function generate(ChannelInterface $channel, string $locale = null, Request $request = null): string;

    /**
     * Returns true if the url generator can generate a URL based on the given arguments
     */
    public function supports(ChannelInterface $channel, string $locale = null, Request $request = null): bool;
}
