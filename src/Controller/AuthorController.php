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

class AuthorController extends AbstractController
{
    #[Route('/api/author', name: 'app_author_list', methods:'GET')]
    public function authorList(AuthorRepository $authorRepository, SerializerInterface $serializer): Response
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
    public function authorInfo(Author $author, SerializerInterface $serializer)
    {
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
    public function authorCreate(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $data = $request->getContent();

        $author = $serializer->deserialize($data, Author::class, 'json');
        $author->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($author);
        $entityManager->flush();
        return new JsonResponse($data, Response::HTTP_CREATED, [
            "location" => "api/author/".$author->getId()
        ], true);
    }

    #[Route('/api/author/{id}', name: 'app_author_update', methods:'PUT')]
    public function authorUpdate(Author $author, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $data = $request->getContent();

        $serializer->deserialize(
            $data,
            Author::class,
            'json',
            ['object_to_populate'=>$author]
        );

        $entityManager->persist($author);
        $entityManager->flush();
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
