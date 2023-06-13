<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\ProductTypeModel;
use App\Dto\ProductDto;

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
     *     path="/server/public/index.php/product", tags={"Product APIs"},
     *     summary="Get All products details",
     *     security={{"Auth_key": {}}},
     *     @OA\Response(response="200", description="Product details retrieved successfully"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="404", description="Not found")
     * )
     */
    public function listAction($productId = null)
    {
        //Validating request
        $this->checkApiAuth('GET');
        $this->productModel = new ProductModel();
        $this->productModel->setProductId($productId);
        $products = $this->productModel->listProducts();

        //header
        header(self::ACCESS_CONTROL_ALLOW_ORIGIN);
        header(self::CONTENT_TYPE_JSON);
        header('Access-Control-Allow-Methods: GET');
        header(self::ACCESS_CONTROL_ALLOW_HEADERS);

        echo json_encode($products);
    }


    /**
     * @OA\Delete(
     *     path="/server/public/index.php/product",  tags={"Product APIs"},
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
            echo json_encode(['message' => 'Invalid request body']);
            return;
        }

        $productIds = $requestBody;

        $this->productModel = new ProductModel();
        $deleteCount = $this->productModel->massDeleteProducts($productIds);

        if ($deleteCount > 0) {
            echo json_encode(['message' => $deleteCount . ' product(s) deleted successfully']);
        } else {
            echo json_encode(['message' => 'No products found or could not be deleted']);
        }
    }

    /**
     * @OA\Post(
     *     path="/server/public/index.php/product", tags={"Product APIs"},
     *     summary="Create a new product",
     *     security={{"Auth_key": {}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="product_name", type="string", example="ManC"),
     *             @OA\Property(property="product_sku", type="string", example="manc"),
     *             @OA\Property(property="product_price", type="number", example=50),
     *             @OA\Property(property="product_type_key", type="string", example="book"),
     *             @OA\Property(
     *                 property="attributes",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="attribute_code", type="string", example="size"),
     *                     @OA\Property(property="attribute_value", type="integer", example=20)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Product created successfully"),
     *     @OA\Response(response="400", description="POST product data is not provided!")
     *     @OA\Response(response="400", description="Missing attribute codes!")
     *     @OA\Response(response="400", description="SKU is present in the system! Please try with the other SKU")
     *     @OA\Response(response="401", description="Unauthorized")
     * )
     */
    public function addAction()
    {
        try {
            //header
            header(self::ACCESS_CONTROL_ALLOW_ORIGIN);
            header(self::CONTENT_TYPE_JSON);
            header('Access-Control-Allow-Methods: POST');
            header(self::ACCESS_CONTROL_ALLOW_HEADERS);

            $this->checkApiAuth('POST');

            $json = file_get_contents('php://input');
            $productData = json_decode($json);
            if (!$productData) {
                echo json_encode(['message' => "POST product data is not provided!"]);
                header('HTTP/1.1 400 POST product data is not provided!');
                die;
            }

            $productDto = new ProductDto($productData);
            $product = $productDto->dtoToEntity();
            $product->validateAndSaveProduct();
        } catch (\Exception $e) {
            header("HTTP/1.1 {$e->getCode()} {$e->getMessage()}");
        }
    }
}
