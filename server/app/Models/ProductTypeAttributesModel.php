<?php

namespace App\Models;

use Inc\Database;

class ProductTypeAttributesModel extends Database
{
	private $table = "product_type_attributes";
	private $attributeCode;
	private $attributeLabel;
	private $productTypeKey;

	public function __construct($attributeCode = null, $attributeLabel = null, $productTypeKey = null)
	{
		$this->attributeCode = $attributeCode;
		$this->attributeLabel = $attributeLabel;
		$this->productTypeKey = $productTypeKey;
		parent::__construct();
	}

	public function getAttributeCode()
	{
		return $this->attributeCode;
	}

	public function setAttributeCode($attributeCode): self
	{
		$this->attributeCode = $attributeCode;
		return $this;
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
		$columns = 'attribute_code, attribute_label';
		$where = 'product_type_key = :product_type_key';
		$params = [':product_type_key' => $this->getProductTypeKey()];

		return $this->select($this->table, $columns, $where, $params);
	}
}
