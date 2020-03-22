<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

/**
 * Enables lockfile-based maintenance mode.
 *
 * Start maintenance mode by `touch var/lock/maintenance.lock` command.
 * Make sure that `var/lock` directory already exists. Stop maintenance
 * using `rm -f var/lock/maintenance.lock` command.
 *
 * This maintenance mode implementation breaks web debug toolbar and profiler.
 */
class MaintenanceModeSubscriber implements EventSubscriberInterface
{
    private $lockFilePath;
    private $twig;

    public function __construct(string $lockFilePath, Environment $twig)
    {
        $this->lockFilePath = $lockFilePath;
        $this->twig = $twig;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (false === file_exists($this->lockFilePath)) {
            return;
        }

        $response = new Response($this->twig->render('maintenance.html.twig'), 503);
        $event->setResponse($response);
    }

    static public function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
