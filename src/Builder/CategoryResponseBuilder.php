<?php
declare(strict_types=1);

namespace App\Builder;

use App\Entity\Category;
use App\Entity\Product;
use App\Response\TaggedJsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouterInterface;

class CategoryResponseBuilder
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForList(array $categories): JsonResponse
    {
        $response = new TaggedJsonResponse(array_map(fn(Category $category) => [
            'name' => $category->getName(),
            'path' => $this->router->generate(
                'display_category',
                ['name' => $category->getName()],
                RouterInterface::ABSOLUTE_URL
            )
        ], $categories));

        $response
            ->setTags(['categories']);

        return $response;
    }

    public function buildForCategory(Category $category): JsonResponse
    {
        $response = new TaggedJsonResponse([
            'id' => $category->getId(),
            'name' => $category->getName(),
            'products' => array_map(fn(Product  $product) => [
                'name' => $product->getName(),
            ], $category->getProducts()->toArray())
        ]);

        $response
            // todo: move to const
            ->setTags([sprintf('category-%s', $category->getId())]);

        return $response;
    }
}