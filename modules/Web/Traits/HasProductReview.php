<?php

namespace Modules\Web\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Poz\Models\ProductReview;

trait HasProductReview
{
    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function addReview(array $data)
    {
        $validator = Validator::make($data, [
            'name'        => 'required|string|max:255',
            'email'       => 'required|email',
            'description' => 'required|string|min:10',
            'rating'      => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->reviews()->create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'description' => $data['description'],
            'rating'      => $data['rating'],
        ]);
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }
}
