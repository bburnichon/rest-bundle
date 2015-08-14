<?php

namespace Alchemy\RestBundle\EventListener;

use Alchemy\Rest\Response\ArrayTransformer;
use Alchemy\Rest\Result\ResourceCreatedResult;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResourceCreatedListener implements EventSubscriberInterface
{

    private $transformer;

    public function __construct(ArrayTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();
        $request = $event->getRequest();

        if (! $result instanceof ResourceCreatedResult) {
            return;
        }

        $transformerKey = $request->attributes->get('_rest[transform]', null, true);
        $transformedData = $this->transformer->transform($transformerKey, $result->getResource());

        if (! isset($transformedData['meta']) && ! count($result->getMetadata())) {
            $transformedData['meta'] = array();
        }

        $transformedData['meta'] = array_merge($transformedData['meta'], $result->getMetadata());

        $event->setResponse(new JsonResponse($transformedData, 201));
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::VIEW => 'onKernelView');
    }
}
