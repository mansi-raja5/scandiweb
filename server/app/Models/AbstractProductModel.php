<?php

namespace App\Models;

use Inc\Database;
use App\Models\ProductInterface;

abstract class AbstractProductModel extends Database implements ProductInterface
{
	private $productId;
	private $userId;
	private $productName;
	private $productSKU;
	private $productPrice;
	private $productTypeKey;
	private $createdAt;
	private $attributes;


	// Constructor
	public function __construct($productId = null, $userId = null, $productName = null, $productSKU = null, $productPrice = null, $productTypeKey = null, $attributes = [])
	{
		$this->productId = $productId;
		$this->userId = $userId;
		$this->productName = $productName;
		$this->productSKU = $productSKU;
		$this->productPrice = $productPrice;
		$this->productTypeKey = $productTypeKey;
		$this->createdAt = date('Y-m-d H:m:s');
		$this->attributes = $attributes;
		parent::__construct();
	}

	abstract public function getProductType();

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

	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	public function getAttributes()
	{
		return $this->attributes;
	}

	public function setAttributes($attributes)
	{
		$this->attributes = $attributes;
	}
}
