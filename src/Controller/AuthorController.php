<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

}
