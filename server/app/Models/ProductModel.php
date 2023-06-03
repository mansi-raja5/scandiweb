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
    private $multiply;

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

    public function getmultiplyPrice()
    {
        return  $this->multiply;
    }

    public function setmultiplyPrice($multiply)
    {
        $this->multiply = $multiply;
    }

    public function listProducts()
    {

        $products = [];
        $condition = $this->getProductId() ? "product_id=" . $this->getProductId() : "1=1";
        $sql = "SELECT * FROM products WHERE $condition";
        $productData = $this->select($sql);
        foreach ($productData as $data) {
            $productId = $data['product_id'];
            $productTypeKey = $data['product_type_key'];
            $products[$productId] = $data;
            $products[$productId]['product_price'] = $this->getmultiplyPrice() ? $products[$productId]['product_price'] * $this->getmultiplyPrice() : $products[$productId]['product_price'];
            $products[$productId]['attributes'] = [];

            // Create a new product attribute and set its value
            $productAttribute = new ProductAttributesModel(null, $productId);
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

        $sql = "DELETE FROM product_attributes WHERE product_id = " . $this->escape_value($this->getProductId());
        $this->query($sql);

        $sql = "DELETE FROM $this->table WHERE product_id = " . $this->escape_value($this->getProductId());
        $this->query($sql);

        return true;
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
            return json_encode(array('Failure' => 'SKU is already present in the system! Please try with the other SKU'));
        }

        $data = array(
            'user_id' => $this->escape_value($this->getUserId()),
            'product_name' => $this->escape_value($this->getProductName()),
            'product_sku' => $this->escape_value($this->getProductSKU()),
            'product_price' => $this->escape_value($this->getProductPrice()),
            'product_type_key' => $this->escape_value($this->getProductTypeKey()),
            'created_at' => $this->escape_value($this->getCreatedAt())
        );

        $productId = $this->insert($this->table, $data);

        // Check if product attributes are provided in the request
        if (is_array($this->getProductAttribute())) {
            foreach ($this->getProductAttribute() as $attribute) {

                $attributeId = $attribute->attribute_id;
                $attributeValue = $attribute->attribute_value;

                // Create a new product attribute
                $productAttribute = new ProductAttributesModel(null, $productId, $attributeId, $attributeValue);

                // Save the product attribute to the database
                $attributeId = $productAttribute->saveProductAttribute();
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
