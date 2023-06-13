<?php

namespace App\Models;

use Inc\Database;

class ProductAttributesModel extends Database
{
	private $table = 'product_attributes';
	private $productAttributesId;
	private $productId;
	private $attributeCode;
	private $attributeValue;

	public function __construct($productAttributesId = null, $productId = null, $attributeCode = null, $attributeValue = null)
	{
		$this->productAttributesId = $productAttributesId;
		$this->productId = $productId;
		$this->attributeCode = $attributeCode;
		$this->attributeValue = $attributeValue;
		parent::__construct();
	}

	public function getProductId()
	{
		return $this->productId;
	}

	public function setProductId($productId)
	{
		$this->productId = $productId;
	}

	public function getAttributeCode()
	{
		return $this->attributeCode;
	}

	public function setAttributeCode($attributeCode)
	{
		$this->attributeCode = $attributeCode;
	}

	public function getAttributeValue()
	{
		return $this->attributeValue;
	}

	public function setAttributeValue($attributeValue)
	{
		$this->attributeValue = $attributeValue;
	}

	/**
	 * Summary of saveProductAttribute
	 * @return bool
	 */
	public function saveProductAttribute()
	{
		//clean data
		$this->productAttributesId = filter_var($this->productAttributesId, FILTER_VALIDATE_INT);
		$this->productId = filter_var($this->productId, FILTER_VALIDATE_INT);
		$this->attributeValue = trim(htmlspecialchars(strip_tags($this->attributeValue)));

		$data = array(
			'product_id' => $this->productId,
			'attribute_code' => $this->attributeCode,
			'attribute_value' => $this->attributeValue,
			'created_at' => date('Y-m-d H:m:s')
		);

		$productAttributeId = $this->save($this->table, $data);

		if ($productAttributeId) {
			return true;
		}
		return false;
	}

	/**
	 * Summary of getProductAttributeById
	 * @return array<array>
	 */
	public function getProductAttributes()
	{
		$columns = 'attribute_code, attribute_value';
		$where = 'product_id = :product_id';
		$params = [':product_id' => $this->getProductId()];
		$attributesData = [];
		$attributesData = $this->select($this->table, $columns, $where, $params);
		$attributes = [];
		foreach ($attributesData as $attributeData) {
			$attributeId = $attributeData['attribute_code'];
			$attributeValue = $attributeData['attribute_value'];

			$attributes[$attributeId]['attribute_code'] = $attributeId;
			$attributes[$attributeId]['attribute_value'] = $attributeValue;
		}
		return $attributes;
	}
}
