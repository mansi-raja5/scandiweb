<?php
namespace App\Models;

class ProductType
{
	private $productTypeKey;
	private $productTypeLabel;

	public function __construct($productTypeKey, $productTypeLabel)
	{
		$this->productTypeKey = $productTypeKey;
		$this->productTypeLabel = $productTypeLabel;
	}

	public function getProductTypeKey()
	{
		return $this->productTypeKey;
	}

	public function setProductTypeKey($productTypeKey)
	{
		$this->productTypeKey = $productTypeKey;
	}

	public function getProductTypeLabel()
	{
		return $this->productTypeLabel;
	}

	public function setProductTypeLabel($productTypeLabel)
	{
		$this->productTypeLabel = $productTypeLabel;
	}
}
