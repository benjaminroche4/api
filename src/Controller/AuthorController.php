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
use Symfony\Contracts\Cache\ItemInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class AuthorController extends AbstractController
{
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Author::class, groups={"full"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="order",
     *     in="query",
     *     description="The field used to order rewards",
     *     @OA\Schema(type="string")
     * )
     * @OA\Tag(name="Author")
     */
    #[Route('/api/author', name: 'app_author_list', methods:'GET')]
    public function authorList(AuthorRepository $authorRepository, SerializerInterface $serializer, CacheInterface $cache): Response
    {
        $authorList = $authorRepository->findAll();

        $result = $serializer->serialize(
            $authorList,
            'json',
            [
                'groups'=>['get:author:list']
            ]
        );
        return new JsonResponse($result, Response::HTTP_OK, [], true);
    }

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

    #[Route('/api/author', name: 'app_author_create', methods:'POST')]
    public function authorCreate(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
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

    #[Route('/api/author/{id}', name: 'app_author_update', methods:'PUT')]
    public function authorUpdate(Author $author, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
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

    #[Route('/api/author/{id}', name: 'app_author_delete', methods:'DELETE')]
    public function authorDelete(Author $author, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($author);
        $entityManager->flush();
        return new JsonResponse('Data deleted', Response::HTTP_OK, [], true);
    }
}
