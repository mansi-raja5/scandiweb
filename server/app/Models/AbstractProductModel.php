<?php

namespace App\Models;

use Inc\Database;

abstract class AbstractProductModel extends Database
{
  protected $productId;
  protected $userId;
  protected $productName;
  protected $productSKU;
  protected $productPrice;
  protected $productTypeKey;
  protected $createdAt;
  protected $productAttributes;


  // Constructor
  public function __construct($productId = null, $userId = null, $productName = null, $productSKU = null, $productPrice = null, $productTypeKey = null)
  {
    $this->productId = $productId;
    $this->userId = $userId;
    $this->productName = $productName;
    $this->productSKU = $productSKU;
    $this->productPrice = $productPrice;
    $this->productTypeKey = $productTypeKey;
    $this->createdAt = date('Y-m-d H:m:s');
    $this->productAttributes = [];
    parent::__construct();

  }

  abstract public function getProductType();
  // Getter and setter methods

  public function getProductId()
  {
    return $this->productId;
  }

  public function setProductId($productId)
  {
    $this->productId = $productId;
  }

  public function getUserId()
  {
    return $this->userId;
  }

  public function setUserId($userId)
  {
    $this->userId = $userId;
  }

  public function getProductName()
  {
    return $this->productName;
  }

  public function setProductName($productName)
  {
    $this->productName = $productName;
  }

  public function getProductSKU()
  {
    return $this->productSKU;
  }

  public function setProductSKU($productSKU)
  {
    $this->productSKU = $productSKU;
  }

  public function getProductPrice()
  {
    return $this->productPrice;
  }

  public function setProductPrice($productPrice)
  {
    $this->productPrice = $productPrice;
  }

  public function getProductTypeKey()
  {
    return $this->productTypeKey;
  }

  public function setProductTypeKey($productTypeKey)
  {
    $this->productTypeKey = $productTypeKey;
  }

  public function getAttribute($attributeKey)
  {
    if (isset($this->productAttributes[$attributeKey])) {
      return $this->productAttributes[$attributeKey];
    }
    return null;
  }

  public function setAttribute($attributeKey, $attributeValue)
  {
    $this->productAttributes[$attributeKey] = $attributeValue;
  }
}