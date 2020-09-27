<?php
declare(strict_types=1);

namespace App\Subscriber;

use App\Response\TaggedResponseInterface;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TaggedResponseEventSubscriber implements EventSubscriberInterface
{
    private SymfonyResponseTagger $responseTagger;

    public function __construct(SymfonyResponseTagger $responseTagger)
    {
        $this->responseTagger = $responseTagger;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['addTagsToResponse', 1000],
        ];
    }

    public function addTagsToResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if (!$response instanceof TaggedResponseInterface) {
            return;
        }

        $tags = $response->getTags();

        if (empty($tags)) {
            return;
        }

        $this->responseTagger->addTags($tags);
    }
}