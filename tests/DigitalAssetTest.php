<?php
use LCI\Salsify\DigitalAsset;

class DigitalAssetTest extends BaseTestCase
{
    /** @var string  */
    protected $test_asset_id = 'test-api-asset';

    protected $source_url = 'https://via.placeholder.com/400x250';

    public function testCanCreateAsset()
    {
        /** @var DigitalAsset $asset */
        $asset = new DigitalAsset(self::getApiInstance());
        $response = $asset
            ->setSourceUrl($this->source_url)
            ->setId($this->test_asset_id)
            ->setName($this->getSampleData()['name'])
            ->setMeta('test-api-property', $this->getSampleData()['test-api-property'])
            ->create();

        $this->assertEquals(
            '201',
            $response->getStatusCode(),
            'Failed to create asset '.$response->getReasonPhrase(). PHP_EOL.
                print_r($asset->toArray(), true).PHP_EOL.
                $response->getBody()
        );
    }

    /**
     * @depends testCanCreateAsset
     */
    public function testCanGetCreatedAsset()
    {
        /** @var DigitalAsset $asset */
        $asset = new DigitalAsset(self::getApiInstance(), $this->test_asset_id);
        $attributes = $asset
            ->get();

        foreach ($this->getSampleData() as $attribute => $value) {
            $actual_value = (isset($attributes['salsify:'.$attribute]) ? $attributes['salsify:'.$attribute] : $attributes[$attribute]);
            $this->assertEquals(
                $value,
                $actual_value,
                'Matching created data with sample data for asset attribute ' . $attribute
            );
        }
    }

    /**
     * @depends testCanGetCreatedAsset
     */
    public function testCanUpdateAsset()
    {
        /** @var DigitalAsset $asset */
        $asset = new DigitalAsset(self::getApiInstance(), $this->test_asset_id);
        $response = $asset
            ->setFromArray($this->getRandData(), true)
            ->update();

        $this->assertEquals(
            '204',
            $response->getStatusCode(),
            'Failed to update asset '.$response->getReasonPhrase(). PHP_EOL.
                $response->getBody()
        );
    }

    /**
     * @depends testCanCreateAsset
     */
    public function testCanRefreshAsset()
    {
        sleep(2);

        /** @var DigitalAsset $asset */
        $asset = new DigitalAsset(self::getApiInstance(), $this->test_asset_id);
        $response = $asset
            ->refresh();

        $this->assertEquals(
            '204',
            $response->getStatusCode(),
            'Failed to refresh asset '.$response->getReasonPhrase(). PHP_EOL.
                $response->getBody(). PHP_EOL.
                print_r(self::getApiInstance()->getRequestData(), true)
        );
    }

    /**
     * @depends testCanCreateAsset
     */
    public function testCanDeleteAsset()
    {
        sleep(3);

        /** @var DigitalAsset $asset */
        $asset = new DigitalAsset(self::getApiInstance(), $this->test_asset_id);
        $response = $asset
            ->delete();

        $this->assertEquals(
            '204',
            $response->getStatusCode(),
            'Failed to delete asset '.$response->getReasonPhrase(). PHP_EOL.
                $response->getBody(). PHP_EOL.
                print_r(self::getApiInstance()->getRequestData(), true)
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
            'source_url' => ( $count === 1 ? $this->source_url : 'https://via.placeholder.com/400x400'),
            'name' => "Test API Asset Name {$count}",
            'test-api-property' => "This asset can be deleted...  {$count}",
        ];
        return $data;
    }

}