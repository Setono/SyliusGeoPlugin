<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\EventSubscriber;

use Setono\SyliusGeoPlugin\Provider\CountryCodeProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class DebugSubscriber implements EventSubscriberInterface
{
    private CountryCodeProviderInterface $countryCodeProvider;

    public function __construct(CountryCodeProviderInterface $countryCodeProvider)
    {
        $this->countryCodeProvider = $countryCodeProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'debug',
        ];
    }

    public function debug(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->query->has('_debugGeo') && !$request->query->has('_debug_geo')) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set('X-Country', $this->countryCodeProvider->getCountryCode() ?? 'Not set');
    }
}
