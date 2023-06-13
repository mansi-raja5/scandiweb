<?php

namespace tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ProductControllerTest extends TestCase
{
    protected $client;
    public function setUp(): void
    {
        echo "Executing setUp() method.\n";
        parent::setUp();
        $this->client = new Client([
            'base_uri' => 'http://mancs-macbook-pro.local/sw/server/public/index.php/',
        ]);
    }

    public function apache_request_headers()
    {
        return [
            'Host' => 'localhost',
            'User-Agent' => 'PHPUnit',
        ];
    }

    public function testGetProducts()
    {
        // Set the headers
        $headers = [
            'Content-Type' => 'application/json',
            'Auth_key' => '$2y$12$MQJ.uYoQKRROdlhPOHLDzueB5eEW3.saS499SiIgojekLb6KRrNaq',
        ];

        $url = 'product';
        $response = $this->client->get($url, ['headers' => $headers]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);

        foreach ($data as $productId => $product) {
            $this->assertArrayHasKey('product_id', $product);
            $this->assertArrayHasKey('product_name', $product);
            $this->assertArrayHasKey('product_sku', $product);
            // Assert other keys as needed

            // Assert specific values
            $this->assertEquals($productId, $product['product_id']);
            $this->assertNotEmpty($product['product_name']);
            $this->assertNotEmpty($product['product_sku']);
            // Assert other values as needed

            // Assert the attributes within each product
            $this->assertArrayHasKey('attributes', $product);
            $this->assertIsArray($product['attributes']);
            $this->assertNotEmpty($product['attributes']);

            foreach ($product['attributes'] as $attributeId => $attribute) {
                $this->assertArrayHasKey('attribute_id', $attribute);
                $this->assertArrayHasKey('attribute_value', $attribute);
                $this->assertArrayHasKey('attribute_label', $attribute);
                // Assert other attribute keys as needed

                // Assert specific attribute values
                $this->assertEquals($attributeId, $attribute['attribute_id']);
                $this->assertNotEmpty($attribute['attribute_value']);
                $this->assertNotEmpty($attribute['attribute_label']);
                // Assert other attribute values as needed
            }
        }
    }

    public function testCreateProduct_Success()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Auth_key' => '$2y$12$MQJ.uYoQKRROdlhPOHLDzueB5eEW3.saS499SiIgojekLb6KRrNaq',
        ];

        $payload = [
            'user_id' => 1,
            'product_name' => 'ManC1534',
            'product_sku' => 'manc' . date('YmdHi'),
            'product_price' => 50,
            'product_type_key' => 'furniture',
            'attributes' => [
                [
                    'attribute_id' => 3,
                    'attribute_value' => 20,
                ],
                [
                    'attribute_id' => 4,
                    'attribute_value' => 30,
                ],
                [
                    'attribute_id' => 5,
                    'attribute_value' => 40,
                ],
            ],
        ];

        $response = $this->client->post('product', [
            'headers' => $headers,
            'json' => $payload,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Product Added Successfully', $responseData['message']);
    }

    public function testCreateProduct_DuplicateSKU()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Auth_key' => '$2y$12$MQJ.uYoQKRROdlhPOHLDzueB5eEW3.saS499SiIgojekLb6KRrNaq',
        ];

        $payload = [
            'user_id' => 1,
            'product_name' => 'ManC1534',
            'product_sku' => 'manc' . date('YmdHi'),
            'product_price' => 50,
            'product_type_key' => 'furniture',
            'attributes' => [
                [
                    'attribute_id' => 3,
                    'attribute_value' => 20,
                ],
                [
                    'attribute_id' => 4,
                    'attribute_value' => 30,
                ],
                [
                    'attribute_id' => 5,
                    'attribute_value' => 40,
                ],
            ],
        ];

        $response = $this->client->post('product', [
            'headers' => $headers,
            'json' => $payload,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('SKU is present in the system! Please try with the other SKU', $responseData['message']);
    }

    /*public function testCreateProduct_MissingAuthKey()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Auth_key' => '',
        ];

        $payload = [
            'user_id' => 1,
            'product_name' => 'ManC1534',
            'product_sku' => 'manc' . date('YmdHi'),
            'product_price' => 50,
            'product_type_key' => 'furniture',
            'attributes' => [
                [
                    'attribute_id' => 3,
                    'attribute_value' => 20,
                ],
                [
                    'attribute_id' => 4,
                    'attribute_value' => 30,
                ],
                [
                    'attribute_id' => 5,
                    'attribute_value' => 40,
                ],
            ],
        ];

        $response = null;
        try {
            $response = $this->client->post('product', [
                'headers' => $headers,
                'json' => $payload,
            ]);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            //$response = $e->getResponse();
        }

        $this->assertEquals(402, $response->getStatusCode());
        $responseData = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals(402, $responseData['status']);
        $this->assertArrayHasKey('msg', $responseData);
        $this->assertEquals('Auth_key is not present', $responseData['msg']);
    }*/

    public function testDeleteProduct_Success()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Auth_key' => '$2y$12$MQJ.uYoQKRROdlhPOHLDzueB5eEW3.saS499SiIgojekLb6KRrNaq'
        ];

        $payload = [46, 47, 48, 49, 50, 51, 52, 53];

        $response = $this->client->delete('product', [
            'headers' => $headers,
            'json' => $payload,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertStringContainsString('product(s) deleted successfully', $responseData['message']);
    }


    public function testDeleteProduct_InvalidRequestBody()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Auth_key' => '$2y$12$MQJ.uYoQKRROdlhPOHLDzueB5eEW3.saS499SiIgojekLb6KRrNaq'
        ];

        $payload = 'invalid';

        $response = $this->client->delete('product', [
            'headers' => $headers,
            'json' => $payload,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Invalid request body', $responseData['message']);
    }

    public function testDeleteProduct_NoProductsFound()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Auth_key' => '$2y$12$MQJ.uYoQKRROdlhPOHLDzueB5eEW3.saS499SiIgojekLb6KRrNaq'
        ];

        $payload = [100, 101];

        $response = $this->client->delete('product', [
            'headers' => $headers,
            'json' => $payload,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('No products found or could not be deleted', $responseData['message']);
    }
}
