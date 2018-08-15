<?php
use LCI\Salsify\Property;

class PropertyTest extends BaseTestCase
{
    /** @var string  */
    protected $test_property_id = 'test-api-property';

    public function testCanCreateProperty()
    {
        /** @var Property $property */
        $property = new Property(self::getApiInstance(), $this->test_property_id);
        $response = $property
            ->fromArray($this->getSampleData())
            ->create();

        $this->assertEquals(
            '201',
            $response->getStatusCode(),
            'Failed to create property '.$response->getReasonPhrase(). PHP_EOL.
                print_r($property->toArray(), true).PHP_EOL.
                $response->getBody()
        );
    }

    /**
     * @depends testCanCreateProperty
     */
    public function testCanGetCreatedProperty()
    {
        /** @var Property $property */
        $property = new Property(self::getApiInstance(), $this->test_property_id);
        $attributes = $property
            ->load();

        foreach ($this->getSampleData() as $attribute => $value) {
            $this->assertEquals(
                $value,
                $attributes[$attribute],
                'Matching created data with sample data for property attribute ' . $attribute
            );
        }
    }

    /**
     * @depends testCanGetCreatedProperty
     */
    public function testCanUpdateProperty()
    {
        /** @var Property $property */
        $property = new Property(self::getApiInstance(), $this->test_property_id);
        $response = $property
            ->fromArray($this->getRandData())
            ->update();

        $this->assertEquals(
            '204',
            $response->getStatusCode(),
            'Failed to update property '.$response->getReasonPhrase(). PHP_EOL.
                $response->getBody()
        );
    }

    /**
     * @depends testCanCreateProperty
     */
    public function testCanDeleteProperty()
    {
        /** @var Property $property */
        $property = new Property(self::getApiInstance(), $this->test_property_id);
        $response = $property
            ->delete();

        $this->assertEquals(
            '204',
            $response->getStatusCode(),
            'Failed to delete property '.$response->getReasonPhrase(). PHP_EOL.
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
            'name' => "Test API Property {$count}",
            'data_type' => 'boolean',
            //'position' => 1,// does not work in the API?
            'help_text' => "This property can be deleted...  {$count}",
            //'attribute_group' => 'API'// Group must exist or it will error out
        ];
        return $data;
    }

}