<?php

namespace App\Models;

interface ProductInterface
{
    public function getProductId();


    public function setProductId($productId);


    public function getUserId();


    public function setUserId($userId);


    public function getProductName();


    public function setProductName($productName);


    public function getProductSKU();


    public function setProductSKU($productSKU);


    public function getProductPrice();


    public function setProductPrice($productPrice);


    public function getProductTypeKey();


    public function setProductTypeKey($productTypeKey);
}
