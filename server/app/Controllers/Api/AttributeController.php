<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProductTypeAttributesModel;


/**
 * Summary of AttributeController
 */
class AttributeController extends BaseController
{
    private $productTypeAttributesModel;

    /**
     * @OA\Post(
     *     path="/attribute", tags={"Attribute APIs"},
     *     summary="Get attributes for a specific product type",
     *     security={{"Auth_key": {}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="product_type_key", type="string", example="furniture")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Attributes retrieved successfully"),
     *     @OA\Response(response="401", description="Unauthorized")
     * )
     */
    public function listAction()
    {
        //header
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Origin, Content-type, Auth_key, Accept');

        $this->checkApiAuth('POST');
        //get the files data
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        if (!$data) {
            header('HTTP/1.1 402 POST Attribute data is not provided!');
            die;
        }

        $productTypeKey = $this->validateParameters($data->product_type_key);
        $this->productTypeAttributesModel = new ProductTypeAttributesModel();
        $this->productTypeAttributesModel->setProductTypeKey($productTypeKey);
        $products = $this->productTypeAttributesModel->getAttributesByType();
        echo json_encode($products);
    }
}
