<?php

namespace App\Dto;

use App\Models\ProductModel;

class ProductDto
{
    private $userId;
    private $productName;
    private $productSku;
    private $productPrice;
    private $productTypeKey;
    private $attributes;

    public function __construct($productData)
    {
        foreach ($productData as $key => $value) {
            $prop = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
            if (property_exists($this, $prop)) {
                $this->$prop = $value;
            }
        }
    }

    public function dtoToEntity()
    {
        return new ProductModel(null, $this->userId, $this->productName, $this->productSku, $this->productPrice, $this->productTypeKey, $this->attributes);
    }
}
