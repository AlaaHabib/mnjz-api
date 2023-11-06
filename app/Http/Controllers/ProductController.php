<?php

namespace App\Http\Controllers;

use App\Constants\ProductConstants;
use App\Http\Requests\FilterProductRequest;
use App\Http\Requests\SortProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Responses\Response;
use App\Models\Product;
use App\Repositories\ProductRepositoryEloquent;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class ProductController extends Controller
{
    public ProductRepositoryEloquent $productRepository;


    public function __construct(ProductRepositoryEloquent $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     summary="Get all products",
     *     tags={"Products"},
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
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         description="Filter criteria",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sortOrder",
     *         in="query",
     *         description="Sort order (asc/desc)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Products not found"
     *     )
     * )
     */
    // Show all products with name, price, and category
    public function index(Request $request)
    {
        $limit = $request->query('limit', null);

        $query = $this->productRepository;
        if ($request->has('search')) {
            $query = $query->search($request->search);
        }
        // filter products by price, and category
        if ($request->has('filter')) {
            $query = $query->filter($request->filter);
        }
        if (!$request->has('search') && !$request->has('filter')) {
            $query = $this->productRepository->getAllProducts();
        }

        // Paginate the results
        // Sort products by name, price, and category
        if ($request->has('sortBy') || $request->has('sortOrder'))
            $result = $query->orderBy($request->sortBy ?? 'created_at', $request->sortOrder ?? 'asc')->paginate($limit);
        else
            $result = $query->orderBy('created_at', 'asc')->paginate($limit);

        $result = new ProductResource($result);
        $result = $result->response()->getData(true);

        return Response::create()
            ->setData($result)
            ->setStatusCode(ResponseStatus::HTTP_OK)
            ->setMessage(__(ProductConstants::RESPONSE_CODES_MESSAGES[ProductConstants::PRODUCT_1001]))
            ->setResponseCode(ProductConstants::PRODUCT_1001)
            ->success();
    }
    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="category_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created"
     *     )
     * )
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->only(
                [
                    'name',
                    'price',
                    'category_id',
                ]
            );
            $this->productRepository->create($data);

            return Response::create()
                ->setMessage(__(ProductConstants::RESPONSE_CODES_MESSAGES[ProductConstants::PRODUCT_1003]))
                ->setStatusCode(ResponseStatus::HTTP_CREATED)
                ->setResponseCode(ProductConstants::PRODUCT_1003)
                ->success();
        } catch (\Throwable $th) {
            return Response::create()
                ->setMessage($th)
                ->setStatusCode(ResponseStatus::HTTP_INTERNAL_SERVER_ERROR)
                ->failure();
        }
    }
    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     summary="Get a product by ID",
     *      tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $product = $this->productRepository->find($id);

            $products[] = $product;
            $result = new ProductResource($products);

            $result = $result->response()->getData(true);

            return Response::create()
                ->setData($result)
                ->setMessage(__(ProductConstants::RESPONSE_CODES_MESSAGES[ProductConstants::PRODUCT_1001]))
                ->setResponseCode(ProductConstants::PRODUCT_1001)
                ->setStatusCode(ResponseStatus::HTTP_OK)
                ->success();
        } catch (\Throwable $th) {
            return Response::create()
                ->setMessage(__(ProductConstants::RESPONSE_CODES_MESSAGES[ProductConstants::PRODUCT_1004]))
                ->setResponseCode(ProductConstants::PRODUCT_1004)
                ->setStatusCode(ResponseStatus::HTTP_NOT_FOUND)
                ->failure();
        }
    }
    /**
     * @OA\Put(
     *     path="/api/v1/products/{id}",
     *     summary="Update a product by ID",
     *      tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="category_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product updated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = $this->productRepository->update($request->all(), $id);

            $products[] = $product;
            $result = new ProductResource($products);

            $result = $result->response()->getData(true);

            return Response::create()
                ->setData($result)
                ->setMessage(__(ProductConstants::RESPONSE_CODES_MESSAGES[ProductConstants::PRODUCT_1002]))
                ->setResponseCode(ProductConstants::PRODUCT_1002)
                ->setStatusCode(ResponseStatus::HTTP_CREATED)
                ->success();
        } catch (\Exception $th) {
            return Response::create()
                ->setMessage(__(ProductConstants::RESPONSE_CODES_MESSAGES[ProductConstants::PRODUCT_1004]))
                ->setResponseCode(ProductConstants::PRODUCT_1004)
                ->setStatusCode(ResponseStatus::HTTP_NOT_FOUND)
                ->failure();
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     summary="Delete a product by ID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->productRepository->softDeleteProduct($id);

            return Response::create()
                ->setMessage(__(ProductConstants::RESPONSE_CODES_MESSAGES[ProductConstants::PRODUCT_1005]))
                ->setResponseCode(ProductConstants::PRODUCT_1005)
                ->setStatusCode(ResponseStatus::HTTP_OK)
                ->success();
        } catch (\Exception $th) {
            return Response::create()
                ->setMessage(__(ProductConstants::RESPONSE_CODES_MESSAGES[ProductConstants::PRODUCT_1004]))
                ->setResponseCode(ProductConstants::PRODUCT_1004)
                ->setStatusCode(ResponseStatus::HTTP_NOT_FOUND)
                ->failure();
        }
    }
}
