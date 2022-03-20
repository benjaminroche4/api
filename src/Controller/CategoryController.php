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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CategoryController extends AbstractController
{
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of all categorys",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Category::class, groups={"get:category:list"}))
     *     )
     * )
     * @OA\Tag(name="Category")
     */
    #[Route('/api/category', name: 'app_category_list', methods: 'GET')]
    public function categoryList(CategoryRepository $categoryRepository, SerializerInterface $serializer,
                                 CacheInterface $cache): Response
    {
        $categoryList = $cache->get('categoryList', function (ItemInterface $item) use ($categoryRepository)
        {
            $item->expiresAfter(3600);
            return $categoryRepository->findAll();
        });

        $result = $serializer->serialize(
            $categoryList,
            'json',
            [
                'groups'=>['get:category:list']
            ]
        );
        return new JsonResponse($result, Response::HTTP_OK, [], true);
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Add a categorys in database",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Category::class, groups={"post:category"}))
     *     )
     * )
     * @OA\Tag(name="Category")
     */
    #[Route('/api/category', name: 'app_category_create', methods: 'POST')]
    public function categoryCreate(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer,
                                   ValidatorInterface $validator)
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

    /**
     * @OA\Response(
     *     response=200,
     *     description="Add a categorys in database",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Category::class, groups={"post:category"}))
     *     )
     * )
     * @OA\Tag(name="Category")
     */
    #[Route('/api/category/{id}', name: 'app_category_update', methods:'PUT')]
    public function categoryUpdate(Category $category, Request $request, EntityManagerInterface $entityManager,
                                   SerializerInterface $serializer, ValidatorInterface $validator)
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

    /**
     * @OA\Response(
     *     response=200,
     *     description="Add a categorys in database",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Category::class))
     *     )
     * )
     * @OA\Tag(name="Category")
     */
    #[Route('/api/category/{id}', name: 'app_category_delete', methods:'DELETE')]
    public function categoryDelete(Category $category, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($category);
        $entityManager->flush();
        return new JsonResponse('Data deleted', Response::HTTP_OK, [], true);
    }

}
