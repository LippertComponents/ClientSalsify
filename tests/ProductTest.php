<?php
use LCI\Salsify\Product;

class ProductTest extends BaseTestCase
{
    /** @var string  */
    protected $test_sku = 'test-api-v1';

    public function testCanCreateProduct()
    {
        /** @var Product $product */
        $product = new Product(self::getApiInstance(), $this->test_sku);
        $response = $product
            ->setProperties($this->getSampleData())
            ->create();

        $this->assertEquals(
            '201',
            $response->getStatusCode(),
            'Failed to create product '.$response->getReasonPhrase(). PHP_EOL.
                $response->getBody()
        );
    }

    /**
     * @depends testCanCreateProduct
     */
    public function testCanGetCreatedProduct()
    {
        /** @var Product $product */
        $product = new Product(self::getApiInstance(), $this->test_sku);
        $properties = $product
            ->get();

        foreach ($this->getSampleData() as $property => $value) {
            $this->assertEquals(
                $value,
                $properties[$property],
                'Matching created data with sample data for property ' . $property
            );
        }
    }

    /**
     * @depends testCanGetCreatedProduct
     */
    public function testCanUpdateProduct()
    {
        /** @var Product $product */
        $product = new Product(self::getApiInstance(), $this->test_sku);
        $response = $product
            ->setProperties($this->getRandData())
            ->update();

        $this->assertEquals(
            '204',
            $response->getStatusCode(),
            'Failed to update product '.$response->getReasonPhrase(). PHP_EOL.
                $response->getBody()
        );
    }

    /**
     * @depends testCanCreateProduct
     */
    public function testCanDeleteProduct()
    {
        /** @var Product $product */
        $product = new Product(self::getApiInstance(), $this->test_sku);
        $response = $product
            ->delete();

        $this->assertEquals(
            '204',
            $response->getStatusCode(),
            'Failed to delete product '.$response->getReasonPhrase(). PHP_EOL.
                $response->getBody()
        );
    }

    /**
     * @return array
     */
    protected function getRandData()
    {
        return $this->getSampleData(rand(10, 9999));
    }

    /**
     * @param int $count
     * @return array
     */
    protected function getSampleData($count = 1)
    {
        $data = [
            //'id' => (!empty($sku) ? $sku : "test-{$rand}"),
            "name" => "Test API Product {$count}",
            "description" => "Description about the product {$count}",
            "short_description" => "Short description about the product {$count}",
            //"active" => 1,
        ];
        return $data;
    }

}