<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProductAttributesModel;
use App\Models\ProductModel;
use App\Models\ApiUsersModel;

class ProductController extends BaseController
{
    /**
     * Summary of productModel
     * @var 
     */
    private $productModel;

    /**
     * Summary of apiUsersModel
     * @var 
     */
    private $apiUsersModel;

    /**
     * Summary of __construct
     * @param \App\Models\ProductModel $productModel
     * @param \App\Models\ApiUsersModel $apiUsersModel
     */
    public function __construct()
    {
        $this->productModel =  new ProductModel();
        $this->apiUsersModel =  new ApiUsersModel();
    }

    /**
     * Summary of listAction
     * @return void
     */
    public function listAction($productId = null)
    {
        //header
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Allow-Headers: Origin, Content-type, Auth_Key, Accept');

        //Validating request
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            // Validating Content type
            if ($_SERVER['CONTENT_TYPE'] === 'application/json') {

                //get the auth_key from the header
                $headers = apache_request_headers();
                $auth_key = $headers['Auth_Key'];
                $this->apiUsersModel->auth_key = $auth_key;

                //Verify the Auth Key
                $Verified = $this->apiUsersModel->verify_AuthKey();
                if ($Verified == TRUE) {
                    $this->productModel->setProductId($productId);
                    $products = $this->productModel->listProducts();
                    echo json_encode($products);

                } else {
                    die(header('HTTP/1.1 401 Unauthorized Key Used'));
                }

            } // if for validating content type ends
            else {
                die(header('HTTP/1.1 204 Content type not valid'));
            }
        } // if for request check ends
        else {
            die(header('HTTP/1.1 400 Request Method is Not Valid'));
        }
    }

    /**
     * Summary of deleteAction
     * @return void
     */
    public function deleteAction($productId = null)
    {
        //header
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
        header('Access-Control-Allow-Methods: DELETE');
        header('Access-Control-Allow-Headers: Origin, Content-type, Auth_Key, Accept');

        //Validating request
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

            // Validating Content type
            if ($_SERVER['CONTENT_TYPE'] === 'application/json') {

                //get the auth_key from the header
                $headers = apache_request_headers();
                $auth_key = $headers['Auth_Key'];

                $this->apiUsersModel->auth_key = $auth_key;

                //Verify the Auth Key
                $Verified = $this->apiUsersModel->verify_AuthKey();

                if ($Verified == TRUE) {
                    $this->productModel->setProductId($productId);
                    if ($this->productModel->deleteProduct()) {
                        echo json_encode(array('message' => 'Product Deleted Successfully'));
                    } else {
                        echo json_encode(array('message' => 'Product cannot be Deleted right now..!'));
                    }
                } else {
                    die(header('HTTP/1.1 401 Unauthorized Key Used'));
                }

            } // if for validating content type ends
            else {
                die(header('HTTP/1.1 204 Content type not valid'));
            }
        } // if for request check ends
        else {
            die(header('HTTP/1.1 400 Request Method is Not Valid'));
        }

    }


    /**
     * Summary of add
     * @return void
     */
    public function addAction()
    {
        //header
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Origin, Content-type, Auth_Key, Accept');

        //Validating request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Validating Content type
            if ($_SERVER['CONTENT_TYPE'] === 'application/json') {

                //get the auth_key from the header
                $headers = apache_request_headers();


                $auth_key = $headers['Auth_Key'];
                $this->apiUsersModel->auth_key = $auth_key;

                //Verify the Auth Key
                $Verified = $this->apiUsersModel->verify_AuthKey();

                if ($Verified == TRUE) {

                    //get the files data
                    $json = file_get_contents('php://input');
                    $data = json_decode($json);
                    if (!$data) {
                        die(header('HTTP/1.1 402 POST product data is not provided!'));
                    } 

                    //Validating parameters
                    if ($this->validate_product_param($data->user_id)) {
                        $userId = $data->user_id;
                    } else {
                        die(header('HTTP/1.1 402 user_id parameter is required'));
                    }

                    if ($this->validate_product_param($data->product_name)) {
                        $productName = $data->product_name;
                    } else {
                        die(header('HTTP/1.1 402 product_name parameter is required'));
                    }

                    if ($this->validate_product_param($data->product_sku)) {
                        $productSku = $data->product_sku;
                    } else {
                        die(header('HTTP/1.1 402 product_sku parameter is required'));
                    }


                    if ($this->validate_product_param($data->product_price)) {
                        $productPrice = $data->product_price;
                    } else {
                        die(header('HTTP/1.1 402 product_price parameter is required'));
                    }


                    if ($this->validate_product_param($data->product_type_key)) {
                        $productTypeKey = $data->product_type_key;
                    } else {
                        die(header('HTTP/1.1 402 product_type_key parameter is required'));
                    }

                    $product = new ProductModel(null, $userId, $productName, $productSku, $productPrice, $productTypeKey);
                    $productId = $product->saveProduct();
                    

                    // Check if product attributes are provided in the request
                    if (isset($data->attributes) && is_array($data->attributes)) {
                        $attributes = $data->attributes;

                        foreach ($attributes as $attribute) {

                            $attributeId = $attribute->attribute_id;
                            $attributeValue = $attribute->attribute_value;

                            // Create a new product attribute
                            $productAttribute = new ProductAttributesModel(null, $productId, $attributeId, $attributeValue);

                            // Save the product attribute to the database
                            $attributeId = $productAttribute->saveProductAttribute();
                        }
                    }

                    //create Article
                    if ($productId) {
                        echo json_encode(array('success' => 'Product Added Successfully'));
                    } else {
                        echo json_encode(array('failure' => 'Product cannot be added right now..!'));
                    }
                } else {
                    die(header('HTTP/1.1 401 Unauthorized Key Used'));
                }

            } // if for validating content type ends
            else {
                die(header('HTTP/1.1 204 Content type not valid'));
            }
        } // if for request check ends
        else {
            die(header('HTTP/1.1 400 Request Method is Not Valid'));
        }
    }

    private function validate_product_param($value)
    {
        if (!empty($value)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getProductModel()
    {
        return $this->productModel;
    }

    /**
     * @param mixed $productModel 
     * @return self
     */
    public function setProductModel($productModel): self
    {
        $this->productModel = $productModel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApiUsersModel()
    {
        return $this->apiUsersModel;
    }

    /**
     * @param mixed $apiUsersModel 
     * @return self
     */
    public function setApiUsersModel($apiUsersModel): self
    {
        $this->apiUsersModel = $apiUsersModel;
        return $this;
    }
}