<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 9/28/18
 * Time: 8:51 AM
 */

namespace LCI\Salsify;

use phpDocumentor\Reflection\Types\This;
use LCI\Salsify\Exceptions\ExportException;

/**
 * Class Exports
 * Ephemeral exports are designed as data dumps from the system, allowing exports of raw data from Salsify.
 * @see https://developers.salsify.com/docs/ephemeral-overview
 *
 * @package LCI\Salsify
 */
class RawExports extends PreV1Routes
{
    /** @var string  */
    protected $entity_type = 'product';

    /** @var string  */
    protected $filter = '';

    /** @var string  */
    protected $format = 'csv';// json?

    /** @var array  */
    protected $include_properties = [];

    /** @var string  */
    protected $product_type = 'all';

    /** @var int ~ in seconds */
    protected $status_check_delay = 5;

    /** @var int ~ how many times to check before failing */
    protected $check_limit = 10;

    /**
     * @param string $key
     * @return $this
     */
    public function addPropertyToInclude($key)
    {
        if (!in_array($key, $this->include_properties)) {
            $this->include_properties[] = $key;
        }
        return $this;
    }

    /**
     * @param string $entity_type
     * @return RawExports
     */
    public function setEntityType(string $entity_type): RawExports
    {
        $this->entity_type = $entity_type;
        return $this;
    }

    /**
     * @param string $filter
     * @return RawExports
     */
    public function setFilter(string $filter): RawExports
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @param string $format
     * @return RawExports
     */
    public function setFormat(string $format): RawExports
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @param array $include_properties
     * @return RawExports
     */
    public function setIncludeProperties(array $include_properties): RawExports
    {
        $this->include_properties = $include_properties;
        return $this;
    }

    /**
     * @param string $product_type
     * @return RawExports
     */
    public function setProductType(string $product_type): RawExports
    {
        $this->product_type = $product_type;
        return $this;
    }

    /**
     * @param int $status_check_delay
     * @return RawExports
     */
    public function setStatusCheckDelay(int $status_check_delay): RawExports
    {
        $this->status_check_delay = $status_check_delay;
        return $this;
    }

    /**
     * @param int $check_limit
     * @return RawExports
     */
    public function setCheckLimit(int $check_limit): RawExports
    {
        $this->check_limit = $check_limit;
        return $this;
    }

    /**
     * @param int $list_id ~ ID of list
     *
     * @return bool|\GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function initExportProductsList($list_id)
    {
        $this->entity_type = 'product';

        $this->filter = "=list:{$list_id}";

        $this->addPropertyToInclude('sku');

        return $this->initExport();
    }

    /**
     *
     * @return bool|\GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function initExportProperties()
    {
        $this->entity_type = 'attribute';

        return $this->initExport();
    }

    /**
     *
     * @return bool|\GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function initExportPropertyValues()
    {
        $this->entity_type = 'attribute_value';

        return $this->initExport();
    }

    /**
     * @param int $list_id ~ ID of list
     *
     * @return bool|\GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function initExportDigitalAssetsList($list_id)
    {
        $this->entity_type = 'digital_asset';

        $this->filter = "=list:{$list_id}";

        return $this->initExport();
    }

    /**
     *
     * @return bool|\GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function initExportRelations()
    {
        $this->entity_type = 'relation';

        return $this->initExport();
    }

    /**
     * @return bool|\GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function initExport()
    {
        $this->setAPIBaseUrl();

        $data = [
            "configuration" => [
                "entity_type" => $this->entity_type,
                "properties" => $this->makePropsString($this->include_properties),
                "include_all_columns" => true,
                "format" => $this->format,
                "product_type" => $this->product_type
            ]
            /**
                duration	null
                end_time	null
                failure_reason	null
                progress	0
                start_time	null
                status
             */
        ];
        if (!empty($this->filter)) {
            $data['configuration']['filter'] = $this->filter;
        }

        $response = $this->api->doRequest('POST', 'export_runs', ['json' => $data]);

        $this->restoreAPIbaseUrl();

        return $response;
    }


    /**
     * @param int $export_id ~ the Salsify Export Run ID
     *
     * @return bool|\GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getExportRunStatus($export_id)
    {
        $this->setAPIBaseUrl();

        $response = $this->api->doRequest('GET', "export_runs/{$export_id}");

        $this->restoreAPIBaseUrl();

        return $response;
    }

    /**
     * @param $export_id
     * @param string $file ~ the full file path to which the report will be written to
     * @param int $attempt
     *
     * @return string
     * @throws ExportException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function saveExportReport($export_id, $file, $attempt=1)
    {
        $exportResponse = $this->getExportRunStatus($export_id);
        $export_data = json_decode($exportResponse->getBody(), true);

        if (isset($export_data['status']) && $export_data['status'] == 'completed') {

            /** @var  $response */
            $guzzleClient = new \GuzzleHttp\Client([
                // You can set any number of default request options.
                'timeout' => 15.0,
                // http://docs.guzzlephp.org/en/latest/request-options.html#http-errors
                'http_errors' => false,
                'verify' => false,//$verify_ssl,
            ]);

            $guzzleClient->request('GET', urldecode($export_data['url']), ['sink' => $file]);

        } else {
            if ($attempt >= $this->check_limit) {
                throw new ExportException('The check limit of ' . $this->check_limit . ' has been reached without success for export id: '. $export_id);
            }

            sleep($this->status_check_delay);

            return $this->saveExportReport($export_id, $file, $attempt + 1);
        }

        return $file;
    }

    /**
     * @param array $properties
     *
     * @return string
     */
    protected function makePropsString($properties)
    {
        $string = '';
        foreach ($properties as $prop) {
            $string .= "'{$prop}',";
        }
        return trim($string, ',');
    }

}