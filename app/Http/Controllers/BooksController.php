<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\IndexBooksRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\IgnoreResponse;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

#[Group('Books', description: 'Gestión del catálogo de libros')]
class BooksController extends Controller
{
    /**
     * Listar libros.
     *
     * Retorna el listado de todos los libros almacenados en formato JSON.
     * Si se envía el parámetro de consulta `category`, filtra los libros por dicha categoría.
     */
    #[Endpoint(
        operationId: 'books.index',
        title: 'Listar libros',
        description: 'Listar todos los libros almacenados o filtrar por la categoría especificada mediante el parámetro `category`.',
    )]
    #[QueryParameter(
        name: 'category',
        description: 'ID de la categoría por la cual filtrar los libros.',
        required: false,
        type: 'integer',
        example: 1,
    )]
    public function index(IndexBooksRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $categoryId = $validated['category'] ?? null;

        $books = Book::query()
            ->with('category')
            ->when(
                $categoryId !== null,
                fn ($query) => $query->where('category_id', (int) $categoryId),
            )
            ->orderBy('id')
            ->get();

        return BookResource::collection($books);
    }

    /**
     * Obtener libro.
     *
     * Retorna el detalle de un libro específico junto con su categoría en formato JSON.
     */
    #[Endpoint(
        operationId: 'books.show',
        title: 'Obtener libro',
        description: 'Obtiene el detalle de un libro específico por su ID.',
    )]
    #[PathParameter(
        name: 'book',
        description: 'ID del libro.',
        required: true,
        type: 'integer',
        example: 1,
    )]
    public function show(Book $book): BookResource
    {
        $book->load('category');

        return new BookResource($book);
    }

    /**
     * Crear libro.
     *
     * Valida y almacena un nuevo libro en la base de datos y retorna el recurso creado en JSON.
     */
    #[Endpoint(
        operationId: 'books.create',
        title: 'Crear libro',
        description: 'Crea un nuevo libro validando título, autor, año de publicación, categoría existente y descripción opcional.',
    )]
    #[IgnoreResponse(200)]
    #[Response(
        status: 201,
        description: 'Libro creado correctamente.',
        type: BookResource::class,
    )]
    public function create(CreateBookRequest $request): BookResource
    {
        $book = DB::transaction(function () use ($request): Book {
            $book = Book::create($request->validated());
            $book->load('category');

            return $book;
        });

        return new BookResource($book);
    }

    /**
     * Actualizar libro.
     *
     * Valida los datos enviados y actualiza un libro existente por su ID.
     */
    #[Endpoint(
        operationId: 'books.update',
        title: 'Actualizar libro',
        description: 'Actualiza los datos de un libro existente validando todos los campos requeridos y la categoría.',
    )]
    #[PathParameter(
        name: 'book',
        description: 'ID del libro a actualizar.',
        required: true,
        type: 'integer',
        example: 1,
    )]
    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $book = DB::transaction(function () use ($request, $book): Book {
            $book->update($request->validated());
            $book->load('category');

            return $book;
        });

        return new BookResource($book);
    }

    /**
     * Eliminar libro.
     *
     * Verifica que el libro exista por su ID antes de eliminarlo y retorna una respuesta JSON.
     */
    #[Endpoint(
        operationId: 'books.delete',
        title: 'Eliminar libro',
        description: 'Verifica la existencia del libro indicado y lo elimina del catálogo.',
    )]
    #[PathParameter(
        name: 'book',
        description: 'ID del libro a eliminar.',
        required: true,
        type: 'integer',
        example: 1,
    )]
    public function delete(int $book): JsonResponse
    {
        return DB::transaction(function () use ($book): JsonResponse {
            $bookModel = Book::find($book);

            if (! $bookModel) {
                return response()->json([
                    'message' => 'Libro no encontrado.',
                ], 404);
            }

            $bookModel->delete();

            return response()->json([
                'message' => 'Libro eliminado correctamente.',
            ]);
        });
    }
}
