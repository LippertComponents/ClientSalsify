<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 8/14/18
 * Time: 11:51 AM
 */

namespace LCI\Salsify;

/**
 * Class Channels
 * @see https://developers.salsify.com/docs/integration-with-salsify
 *
 * @package LCI\Salsify
 */
class Channel extends PreV1Routes
{
    /** @var int ~ in seconds before failing */
    protected $download_timeout = 30;

    /**
     * @return int
     */
    public function getDownloadTimeout(): int
    {
        return $this->download_timeout;
    }

    /**
     * @param int $download_timeout
     * @return Channel
     */
    public function setDownloadTimeout(int $download_timeout): Channel
    {
        $this->download_timeout = $download_timeout;
        return $this;
    }

    /**
     * @param int $channel_id
     * @return bool|\GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getChannelData(int $channel_id)
    {
        $this->setAPIBaseUrl();

        $response = $this->api->doRequest('GET', "channels/{$channel_id}/runs/latest");

        $this->restoreAPIBaseUrl();

        return $response;
    }

    /**
     * @param int $channel_id
     * @param string $file ~ the complete path to which you want to save the channel export
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function saveLatestChannel(int $channel_id, string $file)
    {
        $salsifyResponse = $this->getChannelData($channel_id);

        if ($salsifyResponse->getStatusCode() == 200) {
            $data = json_decode($salsifyResponse->getBody(), true);

            if (isset($data['product_export_url'])) {
                $download_url = $data['product_export_url'];

                /** @var  $response */
                $guzzleClient = new \GuzzleHttp\Client([
                    // You can set any number of default request options.
                    'timeout' => $this->download_timeout,
                    // http://docs.guzzlephp.org/en/latest/request-options.html#http-errors
                    //'http_errors' => false,
                    //'verify' => false,//$verify_ssl,
                ]);

                $guzzleClient->request('GET', $download_url, ['sink' => $file]);

            } else {
                $file = false;
            }

        } else {
            $file = false;
        }

        return $file;
    }
}