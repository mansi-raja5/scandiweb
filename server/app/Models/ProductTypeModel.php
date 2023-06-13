<?php

namespace App\Models;

use App\Models\ProductTypeAttributesModel;
use Inc\Database;

class ProductTypeModel extends Database
{
	private $table = "product_type_attributes";
	private $productTypeKey;
	private $productTypeLabel;

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

	public function getAttributes()
	{
		if ($this->getProductTypeKey()) {
			$columns = 'attribute_code, attribute_label';
			$where = 'product_type_key = :product_type_key';
			$params = [':product_type_key' => $this->getProductTypeKey()];
			return $this->select($this->table, $columns, $where, $params);
		}
	}
}
