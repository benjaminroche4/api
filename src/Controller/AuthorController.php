<?php

namespace App\Controller;

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
                'groups'=>['get:list']
            ]
        );
        return new JsonResponse($result, 200, [], true);
    }
}
