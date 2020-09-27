<?php
declare(strict_types=1);

namespace App\Controller;

use App\Builder\CategoryResponseBuilder;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private CategoryResponseBuilder $responseBuilder;

    public function __construct(
        CategoryRepository $categoryRepository,
        CategoryResponseBuilder $responseBuilder
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * @Route("/", name="display_categories", methods={"GET"})
     */
    public function displayCategoriesList(): JsonResponse
    {
        $categories = $this->categoryRepository->findAll();

        return $this->responseBuilder->buildForList($categories);
    }

    /**
     * @Route("/{name}", name="display_category", methods={"GET"})
     */
    public function displayCategory(string $name): JsonResponse
    {
        $category = $this->categoryRepository->findOneByName($name);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException();
        }

        return $this->responseBuilder->buildForCategory($category);
    }
}