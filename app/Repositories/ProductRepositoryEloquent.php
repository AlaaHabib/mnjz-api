<?php

namespace App\Repositories;

use App\Models\Product;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\ProductRepository;
use App\Validators\ProductValidator;

/**
 * Class ProductRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProductRepositoryEloquent extends BaseRepository implements ProductRepository
{
    function search($search)
    {
        return $this->model->search($search);
    }
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Product::class;
    }

    public function createProduct(array $data)
    {
        return $this->create($data);
    }

    public function updateProduct(array $data, $id)
    {
        $product = $this->find($id);
        if ($product) {
            $this->update($data, $id);
            return $this->find($id);
        }
        return null;
    }

    public function softDeleteProduct($id)
    {
        $product = $this->find($id);
        if ($product) {
            return $product->delete(); // Soft delete the product
        }
        return false;
    }

    // Retrieve all products with name, price, and category
    public function getAllProducts()
    {
        return $this->model->select('name', 'price', 'category_id');
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
