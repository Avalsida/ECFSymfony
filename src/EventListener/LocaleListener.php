<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Psr\Log\LoggerInterface;

class LocaleListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $locale = $session->get('_locale', $request->getDefaultLocale());
        $request->setLocale($locale);

        // Log pour le débogage
        // $this->logger->info('Locale définie à partir de la session : ' . $locale);
    }
}
