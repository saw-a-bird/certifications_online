<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onKernelException(ExceptionEvent $event): void{
        // $code = null;
        // $message = null;

        // $exception = $event->getThrowable();

        // $request = $event->getRequest();
        // $route = $request->get('_route');

        // if ($route == 'try_exam') {
        //     $code = 100;
        // } else {
        //     $code = $this->getStatusCodeFromException($exception);
        //     $message = $exception->getMessage();
        // }

        // // $session = $request->getSession();
        // // $session->set('error_code', );
        // // $session->set('error_message', $exception->getMessage());
        // // $session->set('error_route', );

        // $event->setResponse(new RedirectResponse(
        //     $this->router->generate('_error', array(
        //         'error_code' => $code, 
        //         'error_message' => $message,
        //         'error_route' => $route
        //     )
        // )));
    }

    private function getStatusCodeFromException(Exception $exception): int
    {
        if ($exception instanceof HttpException) {
            return $exception->getStatusCode();
        }

        return 500;
    }
    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}