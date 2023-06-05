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
 *     securityScheme="Auth_key",
 *     name="Auth_key"
 * )
 */
class ProductController extends BaseController
{
    private $productModel;

    const CONTENT_TYPE_JSON = 'Content-type: application/json';
    const ACCESS_CONTROL_ALLOW_ORIGIN = 'Access-Control-Allow-Origin: *';
    const ACCESS_CONTROL_ALLOW_HEADERS = 'Access-Control-Allow-Headers: Origin, Content-type, Auth_key, Accept';

    /**
     * @OA\Get(
     *     path="/server/product/{id}", tags={"Product APIs"},
     *     summary="Get All products details / Get Specific product details",
     *     security={{"Auth_key": {}}},
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
        header(self::ACCESS_CONTROL_ALLOW_ORIGIN);
        header(self::CONTENT_TYPE_JSON);
        header('Access-Control-Allow-Methods: GET');
        header(self::ACCESS_CONTROL_ALLOW_HEADERS);
        //Validating request
        $this->checkApiAuth('GET');
        $this->productModel = new ProductModel();
        $this->productModel->setProductId($productId);
        $products = $this->productModel->listProducts();
        echo json_encode($products);
    }

    /**
     * @OA\Delete(
     *     path="/server/product",  tags={"Product APIs"},
     *     summary="Delete multiple products",
     *     security={{"Auth_key": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Product deleted successfully"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="404", description="404 not found")
     * )
     */
    public function deleteAction()
    {
        //header
        header(self::ACCESS_CONTROL_ALLOW_ORIGIN);
        header(self::CONTENT_TYPE_JSON);
        header('Access-Control-Allow-Methods: DELETE');
        header(self::ACCESS_CONTROL_ALLOW_HEADERS);

        //Validating request
        $this->checkApiAuth('DELETE');

        // Retrieve the request body containing the product IDs to delete
        $requestBody = json_decode(file_get_contents('php://input'), true);
        if (!is_array($requestBody)) {
            echo json_encode(array('message' => 'Invalid request body'));
            return;
        }

        $productIds = $requestBody;

        $this->productModel = new ProductModel();
        $deleteCount = $this->productModel->massDeleteProducts($productIds);

        if ($deleteCount > 0) {
            echo json_encode(array('message' => $deleteCount . ' product(s) deleted successfully'));
        } else {
            echo json_encode(array('message' => 'No products found or could not be deleted'));
        }
    }

    /**
     * @OA\Post(
     *     path="/server/product", tags={"Product APIs"},
     *     summary="Create a new product",
     *     security={{"Auth_key": {}}},
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
        header(self::ACCESS_CONTROL_ALLOW_ORIGIN);
        header(self::CONTENT_TYPE_JSON);
        header('Access-Control-Allow-Methods: POST');
        header(self::ACCESS_CONTROL_ALLOW_HEADERS);

        $this->checkApiAuth('POST');

        //get the files data
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        if (!$data) {
            header('HTTP/1.1 402 POST product data is not provided!');
            die;
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
}
