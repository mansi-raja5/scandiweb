<?php

namespace App\Models;

use Inc\Database;

class ProductAttributesModel extends Database
{
	private $table = 'product_attributes';
	private $productAttributesId;
	private $productId;
	private $attributeId;
	private $attributeValue;
	private $createdAt;

	public function __construct($productAttributesId = null, $productId = null, $attributeId = null, $attributeValue = null)
	{
		$this->productAttributesId = $productAttributesId;
		$this->productId = $productId;
		$this->attributeId = $attributeId;
		$this->attributeValue = $attributeValue;
		$this->createdAt = date('Y-m-d H:m:s');
		parent::__construct();
	}

	public function getProductAttributesId()
	{
		return $this->productAttributesId;
	}

	public function setProductAttributesId($productAttributesId)
	{
		$this->productAttributesId = $productAttributesId;
	}

	public function getProductId()
	{
		return $this->productId;
	}

	public function setProductId($productId)
	{
		$this->productId = $productId;
	}

	public function getAttributeId()
	{
		return $this->attributeId;
	}

	public function setAttributeId($attributeId)
	{
		$this->attributeId = $attributeId;
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
		$this->attributeId = filter_var($this->attributeId, FILTER_VALIDATE_INT);
		$this->attributeValue = trim(htmlspecialchars(strip_tags($this->attributeValue)));
		$this->createdAt = date('Y-m-d H:m:s');

		$data = array(
			'product_id' => $this->escapeValue($this->productId),
			'attribute_id' => $this->escapeValue($this->attributeId),
			'attribute_value' => $this->escapeValue($this->attributeValue),
			'created_at' => $this->escapeValue($this->createdAt)
		);

		$productAttributeId = $this->insert($this->table, $data);

		if ($productAttributeId) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Summary of getProductAttributeById
	 * @return array<array>
	 */
	public function getProductAttributeById()
	{
		//clean data
		$this->productAttributesId = filter_var($this->productAttributesId, FILTER_VALIDATE_INT);
		$this->productId = filter_var($this->productId, FILTER_VALIDATE_INT);
		$this->attributeId = filter_var($this->attributeId, FILTER_VALIDATE_INT);
		$this->attributeValue = trim(htmlspecialchars(strip_tags($this->attributeValue)));
		$this->createdAt = date('Y-m-d H:m:s');

		$attributesData = $this->select("SELECT * FROM product_type_attributes PTA 
		LEFT JOIN product_attributes PA ON PA.attribute_id = PTA.attribute_id 
		WHERE PA.product_id = " . $this->getProductId());
		$attributes = [];
		foreach ($attributesData as $attributeData) {
			$attributeId = $attributeData['attribute_id'];
			$attributeValue = $attributeData['attribute_value'];
			$attributeLabel = $attributeData['attribute_label'];

			$attributes[$attributeId]['attribute_id'] = $attributeId;
			$attributes[$attributeId]['attribute_value'] = $attributeValue;
			$attributes[$attributeId]['attribute_label'] = $attributeLabel;
		}
		return $attributes;
	}
}
