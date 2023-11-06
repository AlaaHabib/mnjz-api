<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return $this->collection->map(function ($product) {
            $categories[] = $product->category;

            return [
                'id'                  => $product->id,
                'name'                => $product->name,
                'price'               => $product->price,
                'category'            => new CategoryResource($categories),
            ];
        });
    }
}
