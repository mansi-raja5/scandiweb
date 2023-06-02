<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ApiUsersModel;
use App\Models\ProductTypeAttributesModel;


/**
 * Summary of AttributeController
 */
class AttributeController extends BaseController
{
    /**
     * Summary of productTypeAttributesModel
     * @var 
     */
    private $productTypeAttributesModel;

    /**
     * Summary of apiUsersModel
     * @var 
     */
    private $apiUsersModel;


    /**
     * @OA\Post(
     *     path="/server/public/index.php/attribute", tags={"Attribute APIs"},
     *     summary="Get attributes for a specific product type",
     *     security={{"Auth_Key": {}}},
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

    public function __construct()
    {
        $this->apiUsersModel = new ApiUsersModel();
        $this->productTypeAttributesModel = new ProductTypeAttributesModel();
    }

    /**
     * Summary of list
     * @return void
     */
    public function listAction()
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

                if (isset($headers['Auth_Key'])) {
                    $this->apiUsersModel->auth_key = $headers['Auth_Key'];
                } else {
                    echo json_encode(['status' => 402, 'msg' => 'Auth_key is not present']);
                    die(header('HTTP/1.1 402 Auth_key is not present'));
                }



                //Verify the Auth Key
                $Verified = $this->apiUsersModel->verify_AuthKey();

                if ($Verified == TRUE) {

                    //get the files data
                    $json = file_get_contents('php://input');
                    $data = json_decode($json);

                    if ($this->validate_product_param($data->product_type_key)) {
                        $productTypeKey = $data->product_type_key;
                    } else {
                        die(header('HTTP/1.1 402 product_type_key parameter is required'));
                    }

                    $this->productTypeAttributesModel->setProductTypeKey($productTypeKey);
                    $products = $this->productTypeAttributesModel->getAttributesByType();
                    echo json_encode($products);

                } else {
                    echo json_encode(['status' => 401, 'msg' => 'Unauthorized Key Used']);
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
     * Summary of validate_product_param
     * @param mixed $value
     * @return bool
     */
    private function validate_product_param($value)
    {
        if (!empty($value)) {
            return true;
        } else {
            return false;
        }
    }
}