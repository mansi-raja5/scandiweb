<?php

namespace App\Models;

use App\Models\ProductType;
use App\Models\AbstractProductModel;
use App\Models\ProductAttributesModel;

// Product class
class ProductModel extends AbstractProductModel
{
    private $productAttribute;
    private $productType;
    private $table = 'products';

    public function __construct($productId = null, $userId = null, $productName = null, $productSKU = null, $productPrice = null, $productTypeKey = null)
    {
        parent::__construct($productId, $userId, $productName, $productSKU, $productPrice, $productTypeKey);
    }
    public function getProductType()
    {
        return $this->productType;
    }
    public function setProductType(ProductType $productType)
    {
        $this->productType = $productType;
    }
    public function listProducts()
    {
        $products = [];
        $condition = $this->getProductId() ? "product_id=" . $this->getProductId() : "1=1";
        $sql = "SELECT * FROM products WHERE $condition";
        $productData = $this->select($sql);
        $productAttribute = new ProductAttributesModel();
        foreach ($productData as $data) {
            $productId = $data['product_id'];
            $products[$productId] = $data;
            $products[$productId]['attributes'] = [];
            // Create a new product attribute and set its value
            $productAttribute->setProductId($productId);
            $products[$productId]['attributes'] = $productAttribute->getProductAttributeById();
        }
        return $products;
    }

    /**
     * Summary of deleteProduct
     * @return bool
     */
    public function deleteProduct()
    {
        //clean data
        $this->setProductId(filter_var($this->getProductId(), FILTER_VALIDATE_INT));
        $sql = "DELETE FROM product_attributes WHERE product_id = " . $this->escapeValue($this->getProductId());
        $this->query($sql);
        $sql = "DELETE FROM $this->table WHERE product_id = " . $this->escapeValue($this->getProductId());
        $this->query($sql);
        return true;
    }

    /**
     * Mass delete products by IDs
     * @param array $productIds The array of product IDs to delete
     * @return int The number of products deleted
     */
    public function massDeleteProducts($productIds)
    {
        // Clean data
        foreach ($productIds as $index => $productId) {
            $productIds[$index] = filter_var($productId, FILTER_VALIDATE_INT);
        }

        // Prepare the placeholders for the IN clause
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));

        // Delete the product attributes
        $attributeSql = "DELETE FROM product_attributes WHERE product_id IN ($placeholders)";
        $this->query($attributeSql, $productIds);

        // Delete the products
        $productSql = "DELETE FROM $this->table WHERE product_id IN ($placeholders)";
        return $this->query($productSql, $productIds);
    }

    /**
     * Summary of saveProduct
     * @return mixed
     */
    public function saveProduct()
    {
        //clean data
        $this->setUserId(filter_var($this->getUserId(), FILTER_VALIDATE_INT));
        $this->setProductName(trim(htmlspecialchars(strip_tags($this->getProductName()))));
        $this->setProductSKU(trim(htmlspecialchars(strip_tags($this->getProductSKU()))));
        $this->setProductPrice(trim(htmlspecialchars(strip_tags($this->getProductPrice()))));
        $this->setProductTypeKey(trim(htmlspecialchars(strip_tags($this->getProductTypeKey()))));
        $this->setCreatedAt(date('Y-m-d H:m:s'));
        // Check if SKU already exists
        $existingProduct = $this->getProductBySKU($this->getProductSKU());
        if (count($existingProduct)) {
            $result = ['Failure' => 'SKU is present in the system! Please try with the other SKU'];
            return json_encode($result);
        }
        $data = array(
            'user_id' => $this->escapeValue($this->getUserId()),
            'product_name' => $this->escapeValue($this->getProductName()),
            'product_sku' => $this->escapeValue($this->getProductSKU()),
            'product_price' => $this->escapeValue($this->getProductPrice()),
            'product_type_key' => $this->escapeValue($this->getProductTypeKey()),
            'created_at' => $this->escapeValue($this->getCreatedAt())
        );
        $productId = $this->insert($this->table, $data);
        // Check if product attributes are provided in the request
        if (is_array($this->getProductAttribute())) {
            foreach ($this->getProductAttribute() as $attribute) {
                $attributeId = $attribute->attribute_id;
                $attributeValue = $attribute->attribute_value;
                $productAttribute = new ProductAttributesModel(null, $productId, $attributeId, $attributeValue);
                $productAttribute->saveProductAttribute();
            }
        }
        if ($productId) {
            return json_encode(array('success' => 'Product Added Successfully'));
        } else {
            return json_encode(array('failure' => 'Product cannot be added right now..!'));
        }
    }
    private function getProductBySKU($sku)
    {
        $sql = "SELECT * FROM products WHERE product_sku = '$sku'";
        return $this->select($sql);
    }
    /**
     * @return mixed
     */
    public function getProductAttribute()
    {
        return $this->productAttribute;
    }
    /**
     * @param mixed $productAttribute
     * @return self
     */
    public function setProductAttribute($productAttribute): self
    {
        $this->productAttribute = $productAttribute;
        return $this;
    }
}
