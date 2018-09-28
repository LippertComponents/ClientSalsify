<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 8/14/18
 * Time: 11:52 AM
 */

namespace LCI\Salsify;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;

class API
{

    // Load Guzzle, Auth and all basics

    // need a Local as well

    /** @var string  */
    protected $base_uri = 'https://app.salsify.com/api/v1/';

    /** @var \GuzzleHttp\Client  */
    protected $client;

    /** @var array  */
    protected $client_headers = [];

    /** @var array filter params [name=>value,...] */
    protected $filter_parameters = [];

    /** @var array  */
    protected $request_data = [];

    /** @var string
     * the organization ID which is unique to each Salsify app instance. The org ID can be found after /orgs/ in the
     * URL path for your Salsify organization, eg. in https://app.salsify.com/app/orgs/9-99999-9999-9999-9999-999999999/products
     * the org ID is 9-99999-9999-9999-9999-999999999.
     */
    protected $salsify_org_id;

    /** @var  string private API key */
    protected $token;

    /**
     * @var bool $verify_ssl ~ will load Client
     */
    protected $verify_ssl = true;

    /**
     * API constructor.
     *
     * @param string $salsify_org_id ~ the organization ID which is unique to each Salsify app instance. The org ID can be found after /orgs/ in the
     * URL path for your Salsify organization, eg. in https://app.salsify.com/app/orgs/9-99999-9999-9999-9999-999999999/products
     * the org ID is 9-99999-9999-9999-9999-999999999.
     * @param string $token ~ see https://help.salsify.com/help/getting-started-api-auth
     * @param string $base_uri ~ default: https://app.salsify.com/api/v1/
     */
    public function __construct($salsify_org_id, $token, $base_uri='https://app.salsify.com/api/v1/')
    {
        $this->salsify_org_id = $salsify_org_id;
        $this->token = $token;
        $this->base_uri = $base_uri;
        $this->client_headers = ['Authorization' => 'Bearer '.$this->token];
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return $this
     */
    public function addFilter($name, $value)
    {
        $this->filter_parameters[$name] = $value;
        return $this;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $options
     *
     * @return bool|\GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function doRequest($method, $path, $options=[])
    {
        // should this be limited to just GET?
        if ( $method == 'GET' ) {
            $params = [];
            if (isset($options['query']) ) {
                $params = $options['query'];
            }
            $options['query'] = $this->buildQuery($params);
        }

        $this->request_data = [
            'method' => $method,
            'path' => $path,
            'options' => $options
        ];

        $path = 'orgs/'.$this->salsify_org_id.'/'.$path;

        $response = false;
        try {
            if (!is_object($this->client) || !$this->client instanceof Client) {
                $this->loadClient();
            }
            $response = $this->client->request($method, $path, $options);

        } catch (RequestException $exception) {
            // @TODO log error
            echo Psr7\str($exception->getRequest());
            if ($exception->hasResponse()) {
                echo Psr7\str($exception->getResponse());
            }
        }

        return $response;
    }

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->base_uri;
    }

    /**
     * @return array
     */
    public function getRequestData()
    {
        return $this->request_data;
    }

    /**
     * @param string $base_uri
     * @return API
     */
    public function setBaseUri(string $base_uri): API
    {
        $this->base_uri = $base_uri;
        return $this;
    }

    /**
     * @param array $client_headers
     * @return API
     */
    public function setClientHeaders(array $client_headers): API
    {
        $this->client_headers = $client_headers;
        return $this;
    }

    /**
     * @param string $salsify_org_id
     * @return API
     */
    public function setSalsifyOrgId(string $salsify_org_id): API
    {
        $this->salsify_org_id = $salsify_org_id;
        return $this;
    }

    /**
     * @param string $token
     * @return API
     */
    public function setToken(string $token): API
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @param bool $verify_ssl
     * @return API
     */
    public function setVerifySsl(bool $verify_ssl): API
    {
        $this->verify_ssl = $verify_ssl;
        return $this;
    }

    /**
     * @param array $params
     * @return array
     */
    protected function buildQuery($params):array
    {
        foreach ($this->filter_parameters as $name => $value) {
            $params[$name] = $value;
        }
        return $params;
    }

    /**
     *
     */
    protected function loadClient()
    {
        $this->client = new Client([
            'base_uri' => $this->base_uri, // Base URI is used with relative requests
            'timeout' => 15.0, // You can set any number of default request options.
            'http_errors' => false, // http://docs.guzzlephp.org/en/latest/request-options.html#http-errors
            'verify' => $this->verify_ssl, // local windows machines sometimes give issues here
            'headers' => $this->client_headers, // http://docs.guzzlephp.org/en/latest/request-options.html#headers
            'version' => 1.0 // http://docs.guzzlephp.org/en/latest/request-options.html#version
        ]);
    }
}