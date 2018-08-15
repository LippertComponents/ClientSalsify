<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 8/15/18
 * Time: 12:09 PM
 */

namespace LCI\Salsify;


class BulkAssets
{
    /** @var API  */
    protected $api;

    /** @var array  */
    protected $assets = [];

    /** @var array  */
    protected $ids = [];

    /**
     * BulkAssets constructor.
     * @param API $api
     */
    public function __construct(API $api)
    {
        $this->api = $api;
    }

    /**
     * @param Asset $asset
     * @return $this
     */
    public function addAsset(Asset $asset)
    {
        $this->assets[] = $asset;
        if (!empty($asset->getId())) {
            $this->addAssetId($asset->getId());
        }
        return $this;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function addAssetId($id)
    {
        $this->ids[] = $id;
        return $this;
    }

    /**
     * @param array $ids
     * @return $this
     */
    public function addManyAssetIds($ids=[])
    {
        $this->ids += $ids;
        return $this;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface ~ successful status codes: 201 create and 204 for update
     */
    public function create()
    {
        // list of Assets
        return $this->api->doRequest(
            'POST',
            'digital_assets',
            ['form_params' => $this->assetsToArray('create')]
        );
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface ~ successful status code 204
     */
    public function delete()
    {
        // list of IDs
        return $this->api->doRequest(
            'DELETE',
            "digital_assets", [
            'form_params' => ['salsify:id' => [$this->ids]]
        ]);
    }

    /**
     * @return array
     */
    public function get()
    {
        $response = $this->api->doRequest(
            'GET',
            "digital_assets", [
            'form_params' => ['ids' => [$this->ids]]
        ]);

        $assets = [];
        if ($response->getStatusCode() == 404) {
            //return ['errors' => ['Sku not found in Salsify']];
        } else {
            $items = json_decode($response->getBody(), true);
            foreach ($items as $item) {
                $asset = new Asset($this->api);
                $asset->setFromArray($item, true);

                $assets[] = $asset;
            }
        }

        return $assets;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface ~ successful status codes: 201 create and 204 for update
     */
    public function refresh()
    {
        // list of IDs
        return $this->api->doRequest(
            'POST',
            "digital_assets/refresh", [
            'form_params' => ['salsify:id' => [$this->ids]]
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface ~ successful status codes: 201 create and 204 for update
     */
    public function update()
    {
        // list of Assets
        return $this->api->doRequest(
            'PUT',
            'digital_assets',
            ['form_params' => $this->assetsToArray('update')]
        );
    }

    /**
     * @param string $type
     * @return array
     */
    protected function assetsToArray($type='create')
    {
        $array = [];
        /** @var Asset $asset */
        foreach ($this->assets as $asset) {
            $array[] = $asset->toArray($type);
        }

        return $array;
    }
}