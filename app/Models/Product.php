<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'price', 'category_id'];

    protected $fieldSearchable = [
        'name'
    ];
    protected $fieldFilterable = [
        'price',
        'category'
    ];

    public function scopeSearch($query, $searchParams)
    {
        $searchArray = explode(';', $searchParams);
        $hasValidCriteria = false; // Initialize a flag to track if any valid criteria were provided

        foreach ($searchArray as $search) {
            list($field, $value) = explode(':', $search);
            if (in_array($field, $this->fieldSearchable)) {
                $query->where($field, 'like', '%' . $value . '%');
                $hasValidCriteria = true; // Set the flag to true if at least one valid criteria is found
            }
        }

        // If no valid criteria were found, return an empty query result
        if (!$hasValidCriteria) {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }

    public function scopefilter($query, $filterParams)
    {
        $filterArray = explode(';', $filterParams);
        $hasValidCriteria = false; // Initialize a flag to track if any valid criteria were provided

        foreach ($filterArray as $filter) {
            list($field, $value) = explode(':', $filter);
            if (in_array($field, $this->fieldFilterable)) {
                if ($field == 'price') {
                    $query->where('price', '>=', $value);
                } else
                    $query->where($field, $value);
                $hasValidCriteria = true; // Set the flag to true if at least one valid criteria is found
            }
        }

        // If no valid criteria were found, return an empty query result
        if (!$hasValidCriteria) {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
