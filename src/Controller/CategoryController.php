<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends AbstractController
{
    #[Route('/api/category', name: 'app_category_list', methods: 'GET')]
    public function categoryList(CategoryRepository $categoryRepository, SerializerInterface $serializer): Response
    {
        $categoryList = $categoryRepository->findAll();
        $result = $serializer->serialize(
            $categoryList,
            'json',
            [
                'groups'=>['get:category:list']
            ]
        );
        return new JsonResponse($result, Response::HTTP_OK, [], true);
    }
}
