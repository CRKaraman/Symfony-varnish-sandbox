<?php
declare(strict_types=1);

namespace App\Subscriber;

use App\Event\CategoryCreatedInvalidateListEvent;
use FOS\HttpCacheBundle\CacheManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CategoryCreatedSubscriber implements EventSubscriberInterface
{
    private CacheManager $cacheManager;

    public function __construct(
        CacheManager $cacheManager
    ) {
        $this->cacheManager = $cacheManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            CategoryCreatedInvalidateListEvent::class => 'purgeCategoriesList',
        ];
    }

    public function purgeCategoriesList(): void
    {
        $this->cacheManager->invalidateTags(['categories']);
    }
}