<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LocaleRedirectionSubscriber implements EventSubscriberInterface
{
    private string $defaultLocale;

    public function __construct(string $defaultLocale = 'fr')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$event->isMainRequest()) {
            return;
        }

        $path = $request->getPathInfo();

        if (str_starts_with($path, '/_') || str_contains($path, '.')) {
            return;
        }

        if ($path === '/') {
            return;
        }

        // 3. Pour toutes les autres pages (ex: /register), on check la locale
        if (!preg_match('#^/(fr|en)(/|$)#', $path)) {
            $preferredLocale = $request->getPreferredLanguage(['en', 'fr']) ?: $this->defaultLocale;

            $redirectUrl = '/' . $preferredLocale . $path;
            $event->setResponse(new RedirectResponse($redirectUrl));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Priorité haute (20) pour agir avant le Router
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
