<?php

namespace App\Models;

use App\Models\ProductTypeModel;
use App\Models\AbstractProductModel;
use App\Models\ProductAttributesModel;

// Product class
class ProductModel extends AbstractProductModel
{
    private $productAttribute;
    private $productType;
    private $table = 'products';

    public function getProductType()
    {
        return $this->productType;
    }
    public function setProductType(ProductTypeModel $productType)
    {
        $this->productType = $productType;
    }
    public function listProducts()
    {
        $products = [];
        $productData = $this->select($this->table);

        $productAttribute = new ProductAttributesModel();
        foreach ($productData as $data) {
            $productId = $data['product_id'];
            $products[$productId] = $data;
            $products[$productId]['attributes'] = [];
            // Create a new product attribute and set its value
            $productAttribute->setProductId($productId);
            $products[$productId]['attributes'] = $productAttribute->getProductAttributes();
        }
        return $products;
    }

    /**
     * Mass delete products by IDs
     * @param array $productIds The array of product IDs to delete
     * @return int The number of products deleted
     */
    public function massDeleteProducts($productIds)
    {
        $tableName = 'product_attributes';
        $where = 'product_id IN';

        $this->delete($tableName, $where, $productIds);
        return $this->delete($this->table, $where, $productIds);
    }

    public function saveProduct()
    {
        $data = array(
            'user_id' => $this->getUserId(),
            'product_name' => $this->getProductName(),
            'product_sku' => $this->getProductSKU(),
            'product_price' => $this->getProductPrice(),
            'product_type_key' => $this->getProductTypeKey(),
            'created_at' => $this->getCreatedAt()
        );
        $productId = $this->save('products', $data);

        // Check if product attributes are provided in the request
        if (is_array($this->getProductAttribute())) {
            foreach ($this->getProductAttribute() as $attribute) {
                $attributeCode = $attribute->attribute_code;
                $attributeValue = $attribute->attribute_value;
                $productAttribute = new ProductAttributesModel(null, $productId, $attributeCode, $attributeValue);
                $productAttribute->saveProductAttribute();
            }
        }
        if ($productId) {
            return json_encode(array('message' => 'Product Added Successfully'));
        } else {
            return json_encode(array('message' => 'Product cannot be added right now..!'));
        }
    }

    public function validateAndSaveProduct()
    {
        $productTypeModel = new ProductTypeModel();
        $productTypeModel->setProductTypeKey($this->getProductTypeKey());

        $this->setProductType($productTypeModel); //abstract method
        $this->setProductAttribute($this->getAttributes());

        $missingAttributeCodes = $this->validateProductAttributes();
        if (!empty($missingAttributeCodes)) {
            echo json_encode(['message' => "Missing attribute codes: " . implode(', ', $missingAttributeCodes)]);
            header('HTTP/1.1 400');
            die;
        }

        // Check if SKU already exists
        $existingProduct = $this->getProductBySKU();
        if (count($existingProduct)) {
            echo json_encode(['message' => 'SKU is present in the system! Please try with the other SKU']);
            header('HTTP/1.1 400');
            die;
        }

        //save product
        echo $this->saveProduct();
    }


    public function getProductBySKU()
    {
        $where = 'product_sku = :product_sku';
        $params = [':product_sku' => $this->getProductSKU()];
        return $this->select($this->table, 'product_id', $where, $params);
    }

    public function getProductAttribute()
    {
        return $this->productAttribute;
    }

    public function setProductAttribute($productAttribute): self
    {
        $this->productAttribute = $productAttribute;
        return $this;
    }

    public function validateProductAttributes()
    {
        $typeAttributes = $this->getProductType()->getAttributes();
        $typeAttributes = array_column($typeAttributes, 'attribute_code');
        $productAttributes = array_column($this->productAttribute, 'attribute_code');

        return array_diff($typeAttributes, $productAttributes);
    }
}
