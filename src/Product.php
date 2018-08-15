<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 8/14/18
 * Time: 11:46 AM
 */

namespace LCI\Salsify;


class Product
{
    /** @var \LCI\Salsify\API */
    protected $api;

    /** @var array  */
    protected $properties = [];

    /** @var string ~ sku or salisfy product ID */
    protected $sku;

    /**
     * Product constructor.
     * @param API $api
     * @param string $sku
     */
    public function __construct(API $api, string $sku)
    {
        $this->api = $api;
        $this->sku = $sku;
    }

    /**
     * https://help.salsify.com/help/basic-product-crud-api
     *
     * CREATE a product - POST https://app.salsify.com/api/v1/orgs/<org_ID>/products
    UPDATE a product - PUT https://app.salsify.com/api/v1/orgs/<org_ID>/products/<product_id>
    READ a product - GET https://app.salsify.com/api/v1/orgs/<org_ID>/products/<product_id>
    DELETE a product - DELETE https://app.salsify.com/api/v1/orgs/<org_ID>/products/<product_id>
     */

    /**
     * @param array $data
     *
     * @return \Psr\Http\Message\ResponseInterface - error will be status code 422, valid is 201
     * @TODO require fields
     */
    public function create()
    {
        $data = $this->properties;
        $data['sku'] = $this->sku;
        return $this->api->doRequest('POST', 'products', ['json' => $data]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete()
    {
        return $this->api->doRequest('DELETE', "products/{$this->sku}");
    }

    /**
     * @return array|mixed
     */
    public function get()
    {
        $query = [
            'query' => [
                'id' => $this->sku
            ]
        ];

        $response = $this->api->doRequest('GET', "products/{$this->sku}", $query);

        if (is_object($response)) {
            $this->properties = json_decode($response->getBody(), true);
        }
        return $this->properties;
    }

    // @TODO method loadFromLocal

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @TODO require fields
     */
    public function update()
    {
        unset($this->properties['id']);
        return $this->api->doRequest(
            'PUT',
            "products/{$this->sku}", [
            'json' => $this->properties
        ]);
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param string $property_name
     * @return Property
     */
    public function getPropertyDefinition($property_name)
    {
        return new Property($this->api, $property_name);
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param \LCI\Salsify\API $api
     * @return $this
     */
    public function setApi(API $api): Product
    {
        $this->api = $api;
        return $this;
    }

    /**
     * @param string $name
     * @param string|array $value
     * @return $this
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
        return $this;
    }

    /**
     * @param array $properties
     * @return Product
     */
    public function setProperties(array $properties): Product
    {
        $this->properties = $properties;
        return $this;
    }
}