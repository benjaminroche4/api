<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Contracts\Cache\ItemInterface;

class AuthorController extends AbstractController
{
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of all authors",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Author::class, groups={"get:author:list"}))
     *     )
     * )
     * @OA\Tag(name="Author")
     */
    #[Route('/api/author', name: 'app_author_list', methods:'GET')]
    public function authorList(AuthorRepository $authorRepository, SerializerInterface $serializer, CacheInterface $cache): Response
    {

        $authorList = $cache->get('authorList', function (ItemInterface $item) use ($authorRepository)
        {
            $item->expiresAfter(3600);
            return $authorRepository->findAll();
        });

        $result = $serializer->serialize(
            $authorList,
            'json',
            [
                'groups'=>['get:author:list']
            ]
        );
        return new JsonResponse($result, Response::HTTP_OK, [], true);
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns the detail of author",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Author::class, groups={"get:author:detail", "get:author:list"}))
     *     )
     * )
     * @OA\Tag(name="Author")
     */
    #[Route('/api/author/{id}', name: 'app_author_detail', methods:'GET')]
    public function authorInfo(?Author $author, SerializerInterface $serializer)
    {
        if($author === null)
        {
            return $this->json([
                'status' => 404,
                'message' => 'Author not found'
            ], 404);
        }
        $result = $serializer->serialize(
            $author,
            'json',
            [
                'groups'=>['get:author:list', 'get:author:detail']
            ]
        );
        return new JsonResponse($result, Response::HTTP_OK, [], true);
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Add a author in the database",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Author::class, groups={"post:author"}))
     *     )
     * )
     * @OA\Tag(name="Author")
     */
    #[Route('/api/author', name: 'app_author_create', methods:'POST')]
    public function authorCreate(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer,
                                 ValidatorInterface $validator)
    {
        $data = $request->getContent();

        $author = $serializer->deserialize($data, Author::class, 'json');
        $author->setCreatedAt(new \DateTimeImmutable());

        $errors = $validator->validate($author);
        if(count($errors)){
            $errorsJson = $serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($author);
        $entityManager->flush();
        return new JsonResponse($data, Response::HTTP_CREATED, [
            "location" => "api/author/".$author->getId()
        ], true);
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Update a author in the database",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Author::class, groups={"post:author"}))
     *     )
     * )
     * @OA\Tag(name="Author")
     */
    #[Route('/api/author/{id}', name: 'app_author_update', methods:'PUT')]
    public function authorUpdate(Author $author, Request $request, EntityManagerInterface $entityManager,
                                 SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $data = $request->getContent();

        $serializer->deserialize(
            $data,
            Author::class,
            'json',
            ['object_to_populate'=>$author]
        );

        $errors = $validator->validate($author);
        if(count($errors)){
            $errorsJson = $serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($author);
        $entityManager->flush();
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Deleted a author in the database",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Author::class))
     *     )
     * )
     * @OA\Tag(name="Author")
     */
    #[Route('/api/author/{id}', name: 'app_author_delete', methods:'DELETE')]
    public function authorDelete(Author $author, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($author);
        $entityManager->flush();
        return new JsonResponse('Data deleted', Response::HTTP_OK, [], true);
    }
}
