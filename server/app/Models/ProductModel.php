<?php

namespace App\Models;

use App\Models\ProductType;
use App\Models\AbstractProductModel;
use App\Models\ProductAttributesModel;


// Product class
class ProductModel extends AbstractProductModel {
    private $productType;
    private $table = 'products';

    public function __construct($productId = null, $userId = null, $productName = null, $productSKU = null, $productPrice = null, $productTypeKey = null)
    {
        parent::__construct($productId, $userId, $productName, $productSKU, $productPrice, $productTypeKey);
    }
    public function getProductType() {
        return $this->productType;
    }

    public function setProductType(ProductType $productType) {
        $this->productType = $productType;
    }
    
    public function listProducts(){

        $products = [];
        $condition = $this->getProductId() ? "product_id=".$this->getProductId() : "1=1";
        $sql = "SELECT * FROM products WHERE $condition";
        $productData = $this->select($sql);
        foreach ($productData as $data) {
            $productId = $data['product_id'];
            $productTypeKey = $data['product_type_key'];
            $products[$productId] = $data;
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
    public function deleteProduct(){
        //clean data
        $this->productId = filter_var($this->productId, FILTER_VALIDATE_INT);

        $sql = "DELETE FROM product_attributes WHERE product_id = ".$this->escape_value($this->productId);
        $this->query($sql);

        $sql = "DELETE FROM $this->table WHERE product_id = ".$this->escape_value($this->productId);
        $this->query($sql);

        return true;
    } 

    /**
     * Summary of saveProduct
     * @return mixed
     */
    public function saveProduct(){
        //clean data
        $this->userId = filter_var($this->userId, FILTER_VALIDATE_INT);
        $this->productName = trim(htmlspecialchars(strip_tags($this->productName)));
        $this->productSKU = trim(htmlspecialchars(strip_tags($this->productSKU)));
        $this->productPrice = trim(htmlspecialchars(strip_tags($this->productPrice)));
        $this->productTypeKey = trim(htmlspecialchars(strip_tags($this->productTypeKey)));
        $this->createdAt = date('Y-m-d H:m:s');

		$data = array(
			'user_id' => $this->escape_value($this->userId),
			'product_name' => $this->escape_value($this->productName),
			'product_sku' => $this->escape_value($this->productSKU),
			'product_price' => $this->escape_value($this->productPrice),
			'product_type_key' => $this->escape_value($this->productTypeKey),
			'created_at' => $this->escape_value($this->createdAt)
		);

		$productId = $this->insert($this->table, $data);
        return $productId;
    } 
}
?>