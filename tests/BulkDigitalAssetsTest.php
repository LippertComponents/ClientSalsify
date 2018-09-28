<?php
use LCI\Salsify\DigitalAsset;
use LCI\Salsify\BulkDigitalAssets;

class BulkDigitalAssetsTest extends BaseTestCase
{
    /** @var string  */
    protected $test_asset_id = 'test-api-asset';

    protected $source_url = '';

    public function testCanCreateBulkAssets()
    {
        /** @var BulkDigitalAssets $bulkAssets */
        $bulkAssets = new BulkDigitalAssets(self::getApiInstance());
        $response = $bulkAssets
            ->addAsset($this->getSampleAsset(1))
            ->addAsset($this->getSampleAsset(2))
            ->addAsset($this->getSampleAsset(3))
            ->create();

        $this->assertEquals(
            '201',
            $response->getStatusCode(),
            'Failed to create bulk assets '.$response->getReasonPhrase(). PHP_EOL.
                $response->getBody(). PHP_EOL.
                print_r(self::getApiInstance()->getRequestData(), true)
        );

        $this->assertEquals(
            true,
            false,
            'Failed.'
        );
    }

    /**
     * @depends testCanCreateBulkAssets
     */
    public function testCanGetCreatedBulkAssets()
    {
        /** @var BulkDigitalAssets $bulkAssets */
        $bulkAssets = new BulkDigitalAssets(self::getApiInstance());
        $assets = $bulkAssets
            ->addAssetId($this->test_asset_id.'-1')
            ->addAssetId($this->test_asset_id.'-2')
            ->addAssetId($this->test_asset_id.'-3')
            ->get();

        /** @var DigitalAsset $asset */
        foreach ($assets as $asset) {
            $count = str_replace($this->test_asset_id.'-', '', $asset->getId());
            $compareAsset = $this->getSampleAsset($count);

            $this->assertEquals(
                $compareAsset->getSourceUrl(),
                $asset->getSourceUrl(),
                'Matching created data with sample data for asset source_url for # ' . $count
            );

            $this->assertEquals(
                $compareAsset->getName(),
                $asset->getName(),
                'Matching created data with sample data for asset name for # ' . $count
            );

            $this->assertEquals(
                $compareAsset->getMetaValue('test-api-property'),
                $asset->getMetaValue('test-api-property'),
                'Matching created data with sample data for asset meta data test-api-property for # ' . $count
            );
        }
    }

    /**
     * @depends testCanGetCreatedBulkAssets
     */
    public function testCanUpdateBulkAssets()
    {
        /** @var BulkDigitalAssets $asset */
        $asset = new BulkDigitalAssets(self::getApiInstance());
        $response = $asset
            ->addAsset($this->getRandData(1))
            ->addAsset($this->getRandData(2))
            ->addAsset($this->getRandData(3))
            ->update();

        $this->assertEquals(
            '204',
            $response->getStatusCode(),
            'Failed to update bulk assets '.$response->getReasonPhrase(). PHP_EOL.
                $response->getBody()
        );
    }

    /**
     * @depends testCanCreateBulkAssets
     */
    public function testCanDeleteBulkAssets()
    {
        /** @var BulkDigitalAssets $bulkAssets */
        $bulkAssets = new BulkDigitalAssets(self::getApiInstance());
        $response = $bulkAssets
            ->addAssetId($this->test_asset_id.'-1')
            ->addAssetId($this->test_asset_id.'-2')
            ->addAssetId($this->test_asset_id.'-3')
            ->delete();

        $this->assertEquals(
            '204',
            $response->getStatusCode(),
            'Failed to delete bulk assets '.$response->getReasonPhrase(). PHP_EOL.
                $response->getBody()
        );
    }

    /**
     * @depends testCanCreateBulkAssets
     */
    public function testCanRefreshBulkAssets()
    {
        /** @var BulkDigitalAssets $bulkAssets */
        $bulkAssets = new BulkDigitalAssets(self::getApiInstance());
        $response = $bulkAssets
            ->addAssetId($this->test_asset_id.'-1')
            ->addAssetId($this->test_asset_id.'-2')
            ->addAssetId($this->test_asset_id.'-3')
            ->refresh();

        $this->assertEquals(
            '204',
            $response->getStatusCode(),
            'Failed to delete bulk assets '.$response->getReasonPhrase(). PHP_EOL.
            $response->getBody()
        );
    }

    /**
     * @return DigitalAsset
     */
    protected function getRandData($count=1)
    {
        return $this->getSampleAsset($count, rand(10, 9999));
    }

    /**
     * @param int $count
     * @param int $rand
     * @return \LCI\Salsify\DigitalAsset
     */
    protected function getSampleAsset($count = 1, $rand=1)
    {
        switch ($count) {
            case 1:
                $source_url = 'https://via.placeholder.com/400x150';
                break;
            case 2:
                $source_url = 'https://via.placeholder.com/400x250';
                break;
            case 3:
                $source_url = 'https://via.placeholder.com/400x350';
                break;
            default:
                $source_url = 'https://via.placeholder.com/400x'.$rand;
                break;
        }

        $asset = new DigitalAsset(self::getApiInstance(), $this->test_asset_id.'-'.$count);
        $asset
            ->setSourceUrl($source_url)
            ->setName("Test API Asset Name {$rand}")
            ->setMeta('test-api-property', "This asset can be deleted...  {$rand}");

        return $asset;
    }

}