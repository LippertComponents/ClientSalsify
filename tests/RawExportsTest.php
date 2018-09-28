<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 9/28/18
 * Time: 11:37 AM
 */

use LCI\Salsify\RawExports;

class RawExportsTest extends BaseTestCase
{
    protected $product_list_id = LIST_ID_PRODUCTS;

    protected $digital_asset_list_id = LIST_ID_DIGITAL_ASSET;

    public function testInitRawExports()
    {
        /** @var RawExports $rawExports */
        $rawExports = new RawExports(self::getApiInstance());

        $this->assertInstanceOf(
            'LCI\Salsify\RawExports',
            $rawExports
        );
    }

    /**
     * @depends testInitRawExports
     */
    public function testDigitalAssetListRawExports()
    {
        /** @var RawExports $rawExports */
        $rawExports = new RawExports(self::getApiInstance());

        /** @var \GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\ResponseInterface $salsifyResponse */
        $salsifyResponse = $rawExports
            ->addPropertyToInclude('name')
            ->addPropertyToInclude('da_classification')
            ->initExportDigitalAssetsList($this->digital_asset_list_id);

        $this->assertEquals(
            '201',
            $salsifyResponse->getStatusCode(),
            'Failed to init a raw export for initExportDigitalAssetsList '.$salsifyResponse->getReasonPhrase(). PHP_EOL.
            $salsifyResponse->getBody()
        );

        $json = json_decode($salsifyResponse->getBody(), true);

        $this->assertArrayHasKey(
            'id',
            $json,
            'A key of id was not returned in the $rawExports->initExportDigitalAssetsList request'
        );

        $file = __DIR__ . '/temp/da_' . $this->digital_asset_list_id . '.csv';

        try {
            $file = $rawExports
                ->saveExportReport($json['id'], $file);

        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            $this->assertEmpty(
                $exception->getMessage(),
                '$rawExports->saveExportReport failed'
            );
        }

        $this->assertFileExists(
            $file,
            '$rawExports->saveExportReport did not save the report contents to disk'
        );
    }

    /**
     * @depends testInitRawExports
     */
    public function testProductListRawExports()
    {
        /** @var RawExports $rawExports */
        $rawExports = new RawExports(self::getApiInstance());

        /** @var \GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\ResponseInterface $salsifyResponse */
        $salsifyResponse = $rawExports
            ->initExportProductsList($this->product_list_id);

        $this->assertEquals(
            '201',
            $salsifyResponse->getStatusCode(),
            'Failed to init a raw export for initExportProductsList '.$salsifyResponse->getReasonPhrase(). PHP_EOL.
            $salsifyResponse->getBody()
        );

        $json = json_decode($salsifyResponse->getBody(), true);

        $this->assertArrayHasKey(
            'id',
            $json,
            'A key of id was not returned in the $rawExports->initExportProductsList request'
        );

        $file = __DIR__ . '/temp/product_' . $this->product_list_id . '.csv';

        try {
            $file = $rawExports
                ->saveExportReport($json['id'], $file);

        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            $this->assertEmpty(
                $exception->getMessage(),
                '$rawExports->saveExportReport failed'
            );
        }

        $this->assertFileExists(
            $file,
            '$rawExports->saveExportReport did not save the report contents to disk'
        );
    }


}