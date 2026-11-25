<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\IgnoreResponse;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

#[Group('Categories', description: 'Gestión de categorías de libros')]
class CategoriesController extends Controller
{
    /**
     * Listar categorías.
     *
     * Retorna todas las categorías ordenadas por ID en formato JSON.
     */
    #[Endpoint(
        operationId: 'categories.index',
        title: 'Listar categorías',
        description: 'Lista todas las categorías almacenadas en formato JSON.',
    )]
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->orderBy('id')
            ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Obtener categoría.
     *
     * Retorna el detalle de una categoría específica por su ID.
     */
    #[Endpoint(
        operationId: 'categories.show',
        title: 'Obtener categoría',
        description: 'Obtiene una categoría específica mediante su ID.',
    )]
    #[PathParameter(
        name: 'category',
        description: 'ID de la categoría.',
        required: true,
        type: 'integer',
        example: 1,
    )]
    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category);
    }

    /**
     * Crear categoría.
     *
     * Valida y almacena una nueva categoría y retorna el recurso creado en JSON.
     */
    #[Endpoint(
        operationId: 'categories.create',
        title: 'Crear categoría',
        description: 'Crea una nueva categoría validando que su nombre sea único.',
    )]
    #[IgnoreResponse(200)]
    #[Response(
        status: 201,
        description: 'Categoría creada correctamente.',
        type: CategoryResource::class,
    )]
    public function create(CreateCategoryRequest $request): CategoryResource
    {
        $category = DB::transaction(
            fn (): Category => Category::create($request->validated()),
        );

        return new CategoryResource($category);
    }

    /**
     * Actualizar categoría.
     *
     * Valida y actualiza el nombre de una categoría existente por su ID.
     */
    #[Endpoint(
        operationId: 'categories.update',
        title: 'Actualizar categoría',
        description: 'Actualiza una categoría existente validando unicidad del nombre.',
    )]
    #[PathParameter(
        name: 'category',
        description: 'ID de la categoría a actualizar.',
        required: true,
        type: 'integer',
        example: 1,
    )]
    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $category = DB::transaction(function () use ($request, $category): Category {
            $category->update($request->validated());

            return $category;
        });

        return new CategoryResource($category);
    }

    /**
     * Eliminar categoría.
     *
     * Verifica la existencia de la categoría y que no tenga libros asociados antes de eliminarla.
     */
    #[Endpoint(
        operationId: 'categories.delete',
        title: 'Eliminar categoría',
        description: 'Verifica la existencia de la categoría y la elimina si no tiene libros asociados.',
    )]
    #[PathParameter(
        name: 'category',
        description: 'ID de la categoría a eliminar.',
        required: true,
        type: 'integer',
        example: 1,
    )]
    public function delete(int $category): JsonResponse
    {
        return DB::transaction(function () use ($category): JsonResponse {
            $categoryModel = Category::find($category);

            if (! $categoryModel) {
                return response()->json([
                    'message' => 'Categoría no encontrada.',
                ], 404);
            }

            if ($categoryModel->books()->exists()) {
                return response()->json([
                    'message' => 'No se puede eliminar la categoría porque tiene libros asociados.',
                ], 409);
            }

            $categoryModel->delete();

            return response()->json([
                'message' => 'Categoría eliminada correctamente.',
            ]);
        });
    }
}
