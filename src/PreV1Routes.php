<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 9/28/18
 * Time: 1:55 PM
 */

namespace LCI\Salsify;


abstract class PreV1Routes
{
    /** @var API  */
    protected $api;

    /** @var string  */
    protected $original_base_url = '';

    /** @var mixed|string  */
    protected $base_url = '';

    /**
     * SimpleRoutes constructor.
     * @param API $api
     */
    public function __construct(API $api)
    {
        $this->api = $api;

        // the v1/ is not valid for these routes, note sure if it will always be that way?
        $this->base_url = str_replace('v1/', '', $this->api->getBaseUri());
        $this->original_base_url = $this->api->getBaseUri();
    }

    /**
     * @return void
     */
    protected function setAPIBaseUrl()
    {
        $this->api->setBaseUri($this->base_url);
    }

    /**
     * @return void
     */
    protected function restoreAPIBaseUrl()
    {
        $this->api->setBaseUri($this->original_base_url);
    }
}