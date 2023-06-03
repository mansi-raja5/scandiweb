<?php
namespace App\Models;

use Inc\Database;

class ProductTypeAttributesModel extends Database
{
	private $attributeId;
	private $attributeKey;
	private $attributeLabel;
	private $productTypeKey;

	public function __construct($attributeId = null, $attributeKey = null, $attributeLabel = null, $productTypeKey = null)
	{
		$this->attributeId = $attributeId;
		$this->attributeKey = $attributeKey;
		$this->attributeLabel = $attributeLabel;
		$this->productTypeKey = $productTypeKey;
		parent::__construct();
	}

	public function getAttributeId()
	{
		return $this->attributeId;
	}

	public function setAttributeId($attributeId)
	{
		$this->attributeId = $attributeId;
	}

	public function getAttributeKey()
	{
		return $this->attributeKey;
	}

	public function setAttributeKey($attributeKey)
	{
		$this->attributeKey = $attributeKey;
	}

	public function getAttributeLabel()
	{
		return $this->attributeLabel;
	}

	public function setAttributeLabel($attributeLabel)
	{
		$this->attributeLabel = $attributeLabel;
	}

	public function getProductTypeKey()
	{
		return $this->productTypeKey;
	}

	public function setProductTypeKey($productTypeKey)
	{
		$this->productTypeKey = $productTypeKey;
	}

	public function getAttributesByType()
	{
		$sql = "SELECT * FROM product_type_attributes WHERE product_type_key = '" . $this->getProductTypeKey() . "'";
		$result = $this->select($sql);
		return $result;
	}
}
