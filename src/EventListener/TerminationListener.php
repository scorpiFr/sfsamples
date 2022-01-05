<?php
// src/EventListener/TerminationListener.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\TerminateEvent;

class TerminationListener
{
    public function onTerminateEvent(TerminateEvent $event)
    {
        // verifications
        $request = $event->getRequest();
        if ($request->get('_route') != 'articles') {
            return;
        }

        // actions
        // file_put_contents("E:/pro/logs/test.log", "toto\n", FILE_APPEND);
    }
}