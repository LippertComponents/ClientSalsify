<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 9/28/18
 * Time: 11:37 AM
 */

use LCI\Salsify\RawExports;
use LCI\Salsify\Helpers\PropertyValues;

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

    /**
     * @depends testInitRawExports
     */
    public function testPropertiesRawExports()
    {
        /** @var RawExports $rawExports */
        $rawExports = new RawExports(self::getApiInstance());

        /** @var \GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\ResponseInterface $salsifyResponse */
        $salsifyResponse = $rawExports
            ->initExportProperties();

        $this->assertEquals(
            '201',
            $salsifyResponse->getStatusCode(),
            'Failed to init a raw export for initExportProperties '.$salsifyResponse->getReasonPhrase(). PHP_EOL.
            $salsifyResponse->getBody()
        );

        $json = json_decode($salsifyResponse->getBody(), true);

        $this->assertArrayHasKey(
            'id',
            $json,
            'A key of id was not returned in the $rawExports->initExportProperties request'
        );

        $file = __DIR__ . '/temp/properties.csv';

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
    public function testPropertyValuesRawExports()
    {
        /** @var RawExports $rawExports */
        $rawExports = new RawExports(self::getApiInstance());

        /** @var \GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\ResponseInterface $salsifyResponse */
        $salsifyResponse = $rawExports
            ->initExportPropertyValues();

        $this->assertEquals(
            '201',
            $salsifyResponse->getStatusCode(),
            'Failed to init a raw export for initExportPropertyValues '.$salsifyResponse->getReasonPhrase(). PHP_EOL.
            $salsifyResponse->getBody()
        );

        $json = json_decode($salsifyResponse->getBody(), true);

        $this->assertArrayHasKey(
            'id',
            $json,
            'A key of id was not returned in the $rawExports->initExportPropertyValues request'
        );

        $file = __DIR__ . '/temp/property_values.csv';

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
     * @depends testPropertyValuesRawExports
     */
    public function testPropertyValuesHelper()
    {
        $helper = new PropertyValues();
        $helper->loadSourceFromCsv(__DIR__ . '/temp/property_values.csv');

        $rows = $helper->getPropertyValues('category');

        $this->assertGreaterThan(
            0,
            count($rows),
            count($rows).' rows where processed'
        );

        file_put_contents(__DIR__ . '/temp/property_rows.php', $this->prettyVarExport($rows));

        $nested = $helper->getPropertyValuesNested('category');

        $this->assertGreaterThan(
            0,
            count($nested),
            count($nested). ' nested rows where processed'
        );

        file_put_contents(__DIR__ . '/temp/property_nested_rows.php', $this->prettyVarExport($nested));
    }

    /**
     * @param mixed|array $data
     * @param int $tabs
     *
     * @return string
     */
    protected function prettyVarExport($data, $tabs = 1)
    {
        $spacing = str_repeat(' ', 4 * $tabs);

        $string = '';
        $parts = preg_split('/\R/', var_export($data, true));
        foreach ($parts as $k => $part) {
            if ($k > 0) {
                $string .= $spacing;
            }
            $string .= $part.PHP_EOL;
        }

        return '<?php' . PHP_EOL . trim($string) . ';';
    }
}