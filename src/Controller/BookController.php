<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
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

class BookController extends AbstractController
{
    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of all books",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Book::class, groups={"get:book:list"}))
     *     )
     * )
     * @OA\Tag(name="Book")
     */
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

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns the detail of book",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Book::class, groups={"get:book:list", "get:book:detail"}))
     *     )
     * )
     * @OA\Tag(name="Book")
     */
    #[Route('/api/book/{id}', name: 'app_book_detail', methods: 'GET')]
    public function bookDetail(?Book $book, SerializerInterface $serializer)
    {
        if($book === null)
        {
            return $this->json([
                'status' => 404,
                'message' => 'Product not found'
            ], 404);
        }
        $result = $serializer->serialize(
            $book,
            'json',
            [
                'groups'=>['get:book:list', 'get:book:detail']
            ]
        );
        return new JsonResponse($result, Response::HTTP_OK, [], true);
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Add book in the database",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Book::class, groups={"post:book"}))
     *     )
     * )
     * @OA\Tag(name="Book")
     */
    #[Route('/api/book', name: 'app_book_create', methods: 'POST')]
    public function bookCreate(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $data = $request->getContent();

        $book = $serializer->deserialize($data, Book::class, 'json');
        $book->setCreatedAt(new \DateTimeImmutable());

        $errors = $validator->validate($book);
        if(count($errors)){
            $errorsJson = $serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($book);
        $entityManager->flush();
        return new JsonResponse($data, Response::HTTP_CREATED, [
            "location" => "api/book/".$book->getId()
        ], true);
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Update book in the database",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Book::class, groups={"post:book"}))
     *     )
     * )
     * @OA\Tag(name="Book")
     */
    #[Route('/api/book/{id}', name: 'app_book_update', methods:'PUT')]
    public function bookUpdate(Book $book, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $data = $request->getContent();

        $serializer->deserialize(
            $data,
            Book::class,
            'json',
            ['object_to_populate'=>$book]
        );

        $errors = $validator->validate($book);
        if(count($errors)){
            $errorsJson = $serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($book);
        $entityManager->flush();
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Deleted book in the detabase",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Book::class))
     *     )
     * )
     * @OA\Tag(name="Book")
     */
    #[Route('/api/book/{id}', name: 'app_book_delete', methods:'DELETE')]
    public function bookDelete(Book $book, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($book);
        $entityManager->flush();
        return new JsonResponse('Data deleted', Response::HTTP_OK, [], true);
    }

}
