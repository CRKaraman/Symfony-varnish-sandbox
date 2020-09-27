<?php
declare(strict_types=1);

namespace App\Subscriber;

use App\Entity\Category;
use App\Event\ProductCreatedEvent;
use App\Repository\CategoryRepository;
use FOS\HttpCacheBundle\CacheManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductCreatedSubscriber implements EventSubscriberInterface
{
    private CategoryRepository $categoryRepository;
    private CacheManager $cacheManager;

    public function __construct(
        CategoryRepository $categoryRepository,
        CacheManager $cacheManager
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->cacheManager = $cacheManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            ProductCreatedEvent::class => 'purgeRelatedCategoriesCache',
        ];
    }

    public function purgeRelatedCategoriesCache(ProductCreatedEvent $event): void
    {
        $categories = $this->categoryRepository->findCategoriesByProductId($event->getProductId());

        $tags = [];
        foreach ($categories as $category) {
            if (!$category instanceof Category) {
                continue;
            }

            $tags[] = sprintf('category-%s', $category->getId());
        }

        $this->cacheManager->invalidateTags($tags);
    }
}