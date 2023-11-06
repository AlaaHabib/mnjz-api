<?php

namespace App\Http\Controllers;

use App\Constants\ProductConstants;
use App\Http\Requests\FilterProductRequest;
use App\Http\Requests\SortProductRequest;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Responses\Response;
use App\Models\Product;
use App\Repositories\CategoryRepositoryEloquent;
use App\Repositories\ProductRepositoryEloquent;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

/**
 * @group Categories
 * APIs for managing categories
 */
class CategoryController extends Controller
{
    public CategoryRepositoryEloquent $categoryRepository;


    public function __construct(CategoryRepositoryEloquent $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all categories
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *     path="/api/v1/categories",
     *     summary="Get all categories",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Limit the number of results",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *          name="page",
     *          description="page",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $limit = $request->query('limit', null);

        $query = $this->categoryRepository->getAllCategories();

        // Paginate the results
        $result = $query->orderBy('created_at', 'asc')->paginate($limit);

        $result = new CategoryResource($result);
        $result = $result->response()->getData(true);

        return Response::create()
            ->setData($result)
            ->setStatusCode(ResponseStatus::HTTP_OK)
            ->setMessage(__(ProductConstants::RESPONSE_CODES_MESSAGES[ProductConstants::CATEGORY_2002]))
            ->setResponseCode(ProductConstants::CATEGORY_2002)
            ->success();
    }

    /**
     * Create a new category
     *
     * @param \App\Http\Requests\StoreCategoryRequest $request
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     *     path="/api/v1/categories",
     *     summary="Create a new category",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created"
     *     )
     * )
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->only(
            [
                'name',
            ]
        );
        $this->categoryRepository->create($data);

        return Response::create()
            ->setMessage(__(ProductConstants::RESPONSE_CODES_MESSAGES[ProductConstants::CATEGORY_2001]))
            ->setStatusCode(ResponseStatus::HTTP_CREATED)
            ->setResponseCode(ProductConstants::CATEGORY_2001)
            ->success();
    }
}
