<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private RouterInterface $router;

    public function __construct(
        CategoryRepository $categoryRepository,
        RouterInterface $router
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->router = $router;
    }

    /**
     * @Route("/", name="display_categories", methods={"GET"})
     */
    public function displayCategoriesList(): JsonResponse
    {
        $categories = $this->categoryRepository->findAll();

        return new JsonResponse(array_map(fn(Category $category) => [
            'name' => $category->getName(),
            'path' => $this->router->generate(
                'display_category',
                ['name' => $category->getName()],
                RouterInterface::ABSOLUTE_URL
            )
        ], $categories));
    }

    /**
     * @Route("/{name}", name="display_category", methods={"GET"}, requirements={"name" = "\w+"})
     */
    public function displayCategory(string $name): JsonResponse
    {
        $category = $this->categoryRepository->findOneByName($name);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse([
            'id' => $category->getId(),
            'name' => $category->getName(),
            'products' => []
        ]);
    }
}