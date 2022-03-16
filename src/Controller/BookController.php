<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    #[Route('/api/book', name: 'app_book_list', methods: 'GET')]
    public function bookList(BookRepository $bookRepository, SerializerInterface $serializer): Response
    {
        $bookList = $bookRepository->findAll();
        $result = $serializer->serialize(
            $bookList,
            'json',
            [
                'groups'=>['get:book:list']
            ]
        );
        return new JsonResponse($result, Response::HTTP_OK, [], true);
    }

    #[Route('/api/book/{id}', name: 'app_book_detail', methods: 'GET')]
    public function bookDetail(Book $book, SerializerInterface $serializer)
    {
        $result = $serializer->serialize(
            $book,
            'json',
            [
                'groups'=>['get:book:list', 'get:book:detail']
            ]
        );
        return new JsonResponse($result, Response::HTTP_OK, [], true);
    }
}
