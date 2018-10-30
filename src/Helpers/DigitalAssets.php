<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 10/24/18
 * Time: 6:22 AM
 */

namespace LCI\Salsify\Helpers;

use LCI\Salsify\API;
use LCI\Salsify\DigitalAsset;

class DigitalAssets
{
    use Source;

    /** @var API  */
    protected $api;

    /**
     * DigitalAssets constructor.
     * @param API $api
     */
    public function __construct(API $api)
    {
        $this->api = $api;
    }

    /**
     * @return array ~ ['salsify:id' => DigitalAsset, ... ]
     */
    public function getAssetsAsDigitalAsset()
    {
        $assets = [];
        foreach ($this->flat_source as $row) {
            $id = '';
            if (isset($row['salsify:id'])) {
                $id = $row['salsify:id'];
            }
            $da = new DigitalAsset($this->api, $id);
            $assets[$id] = $da->setFromArray($row);
        }

        return $assets;
    }
}