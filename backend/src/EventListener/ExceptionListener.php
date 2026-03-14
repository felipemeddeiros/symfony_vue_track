<?php

namespace App\EventListener;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationException) {
            $response = new JsonResponse(
                $exception->getErrors(),
                422
            );
            $event->setResponse($response);
        } elseif ($exception instanceof NotFoundHttpException) {
            $response = new JsonResponse(
                ['error' => 'Resource not found'],
                404
            );
            $event->setResponse($response);
        }
    }
}
