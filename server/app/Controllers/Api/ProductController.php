<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProductModel;

/**
 * @OA\Info(
 *     title="Scandiweb APIs",
 *     version="1.0"
 * ),
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="Auth_Key",
 *     name="Auth_Key"
 * )
 */
class ProductController extends BaseController
{
    /**
     * Summary of productModel
     * @var 
     */
    private $productModel; //final
    /**
     * Summary of __construct
     * @param \App\Models\ProductModel $productModel
     * @param \App\Models\ApiUsersModel $apiUsersModel
     */
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new ProductModel();
    }
    /**
     * @OA\Get(
     *     path="/server/public/index.php/product/{id}", tags={"Product APIs"},
     *     summary="Get All products details / Get Specific product details",
     *     security={{"Auth_Key": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=false,
     *         description="Product ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Product details retrieved successfully"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="404", description="Not found")
     * )
     */
    public function listAction($productId = null)
    {
        //header
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Allow-Headers: Origin, Content-type, Auth_Key, Accept');
        //Validating request
        $this->checkApiAuth('GET');
        $this->productModel->setProductId($productId);
        $this->productModel->setmultiplyPrice(2);
        $products = $this->productModel->listProducts();
        echo json_encode($products);
    }
    /**
     * @OA\Delete(
     *     path="/server/public/index.php/product/{id}",  tags={"Product APIs"},
     *     summary="Delete a product",
     *     security={{"Auth_Key": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Product deleted successfully"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="404", description="Product not found")
     * )
     */
    public function deleteAction($productId = null)
    {
        //header
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
        header('Access-Control-Allow-Methods: DELETE');
        header('Access-Control-Allow-Headers: Origin, Content-type, Auth_Key, Accept');
        //Validating request

        $this->checkApiAuth('DELETE');
        $this->productModel->setProductId($productId);
        if ($this->productModel->deleteProduct()) {
            echo json_encode(array('message' => 'Product Deleted Successfully'));
        } else {
            echo json_encode(array('message' => 'Product cannot be Deleted right now..!'));
        }
    }
    /**
     * @OA\Post(
     *     path="/server/public/index.php/product", tags={"Product APIs"},
     *     summary="Create a new product",
     *     security={{"Auth_Key": {}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="product_name", type="string", example="ManC"),
     *             @OA\Property(property="product_sku", type="string", example="manc"),
     *             @OA\Property(property="product_price", type="number", example=50),
     *             @OA\Property(property="product_type_key", type="string", example="furniture"),
     *             @OA\Property(
     *                 property="attributes",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="attribute_id", type="integer", example=3),
     *                     @OA\Property(property="attribute_value", type="integer", example=20)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Product created successfully"),
     *     @OA\Response(response="401", description="Unauthorized")
     * )
     */
    public function addAction()
    {
        //header
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Origin, Content-type, Auth_Key, Accept');

        $this->checkApiAuth('POST');

        //get the files data
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        if (!$data) {
            die(header('HTTP/1.1 402 POST product data is not provided!'));
        }

        try {
            $userId = $this->validateParameters($data->user_id);
            $productName = $this->validateParameters($data->product_name);
            $productSku = $this->validateParameters($data->product_sku);
            $productPrice = $this->validateParameters($data->product_price);
            $productTypeKey = $this->validateParameters($data->product_type_key);
            $attributes = $this->validateParameters($data->attributes);
            $product = new ProductModel(null, $userId, $productName, $productSku, $productPrice, $productTypeKey);
            $product->setProductAttribute($attributes);
            echo $product->saveProduct();
        } catch (\Exception $e) {
            header("HTTP/1.1 {$e->getCode()} {$e->getMessage()}");
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
}
