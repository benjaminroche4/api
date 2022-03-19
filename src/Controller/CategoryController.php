<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    #[Route('/api/category', name: 'app_category_create', methods: 'POST')]
    public function categoryCreate(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $data = $request->getContent();

        $category = $serializer->deserialize($data, Category::class, 'json');

        $errors = $validator->validate($category);
        if(count($errors)){
            $errorsJson = $serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($category);
        $entityManager->flush();
        return new JsonResponse($data, Response::HTTP_CREATED, [
            "location" => "api/category/".$category->getId()
        ], true);
    }

    #[Route('/api/category/{id}', name: 'app_category_update', methods:'PUT')]
    public function categoryUpdate(Category $category, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $data = $request->getContent();

        $serializer->deserialize(
            $data,
            Category::class,
            'json',
            ['object_to_populate'=>$category]
        );

        $errors = $validator->validate($category);
        if(count($errors)){
            $errorsJson = $serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($category);
        $entityManager->flush();
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/category/{id}', name: 'app_category_delete', methods:'DELETE')]
    public function categoryDelete(Category $category, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($category);
        $entityManager->flush();
        return new JsonResponse('Data deleted', Response::HTTP_OK, [], true);
    }

}
