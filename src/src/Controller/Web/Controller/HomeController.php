<?php
declare(strict_types = 1);

namespace App\Controller\Web\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return new JsonResponse([]);
    }
}