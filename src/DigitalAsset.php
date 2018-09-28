<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 8/14/18
 * Time: 11:50 AM
 */

namespace LCI\Salsify;

use LCI\Salsify\Helpers\Syntax;

class DigitalAsset
{
    use Syntax;

    /** @var API  */
    protected $api;

    /** @var string */
    protected $id;

    /** @var null|string  */
    protected $name = null;
    /** @var string|null */
    protected $source_url;
    /** @var string|null */
    protected $filename;

    /** @var array Asset meta,  name => value */
    protected $meta = [];

    // Salsify sets, not to update:
    /** @var int|null */
    protected $bytes;// 22222,
    /** @var string|null */
    protected $created_at; //"2015-07-07T13:03:54.495Z",
    /** @var string|null */
    protected $format; //"png",
    /** @var int|null */
    protected $height; // 123 ~ asset_height
    /** @var bool  */
    protected $is_primary_image = true; //": true,
    /** @var string|null */
    protected $resource_type; // "image" ~ asset_resource_type
    /** @var string|null */
    protected $status;
    /** @var string|null */
    protected $updated_at; //"2015-07-07T13:03:54.495Z",
    /** @var string|null */
    protected $url;
    /** @var int|null */
    protected $width; //123, ~ asset_width


    /**
     * Asset constructor.
     * @param API $api
     * @param string $id
     */
    public function __construct(API $api, string $id=null)
    {
        $this->api = $api;
        $this->id = $id;
    }


    /**
     * @param array $data
     * @return $this
     */
    public function fromArray($data)
    {
        return $this->setFromArray($data);
    }

    /**
     * @param array $data
     * @param bool $set_protected
     * @return $this
     */
    public function setFromArray(array $data, bool $set_protected=false)
    {
        foreach ($data as $key => $value) {
            if (empty($key)) {
                continue;
            }

            // fixing the salisfy:name
            $parts = explode(':', $key);
            if  (count($parts) > 1) {
                // and make it uniform, no asset_ prefix
                $key = str_replace('asset_', '', $parts[1]);
            }

            $method_name = 'set'.$this->makeStudyCase($key);

            if (method_exists($this, $method_name) && $method_name !== 'setFromArray') {
                $this->$method_name($value);

            } elseif ($set_protected && property_exists($this, $key)) {
                $this->$key = $value;

            } else {
                $this->setMeta($key, $value);

            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getSourceUrl()
    {
        return $this->source_url;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @param $name
     * @return bool|mixed
     */
    public function getMetaValue($name)
    {
        if (isset($this->meta[$name])) {
            return $this->meta[$name];
        }

        return false;
    }

    /**
     * @return int
     */
    public function getBytes(): ?int
    {
        return $this->bytes;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @return int
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @return bool
     */
    public function isPrimaryImage(): bool
    {
        return $this->is_primary_image;
    }

    /**
     * @return string
     */
    public function getResourceType(): ?string
    {
        return $this->resource_type;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return int
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @param string $type ~ create|update|all
     * @return array
     */
    public function toArray($type='all')
    {
        $array = [];
        switch ($type) {
            case 'create':
                $array['salsify:source_url'] = $this->getSourceUrl();
                if (!empty($this->getId())) {
                    $array['salsify:id'] = $this->getId();
                }
                if (!empty($this->getName())) {
                    $array['salsify:name'] = $this->getName();
                }
                break;
            case 'update':
                // @TODO future, should allow the ID to be updated?? would require minor refactor
                if (!empty($this->getName())) {
                    $array['salsify:name'] = $this->getName();
                }
                if (!empty($this->getSourceUrl())) {
                    $array['salsify:source_url'] = $this->getSourceUrl();
                }

                break;
            case 'all':
                // no break
            default:
                $array = [
                    'salsify:source_url' => $this->getSourceUrl(),
                    "salsify:id" => $this->getId(),
                    "salsify:name" => $this->getName(),
                    "salsify:url" => $this->getUrl(),
                    "salsify:filename" => $this->getFilename(),
                    "salsify:is_primary_image" => $this->isPrimaryImage(),
                    "salsify:created_at" => $this->getCreatedAt(),
                    "salsify:updated_at" => $this->getUpdatedAt(),
                    "salsify:bytes" => $this->getBytes(),
                    "salsify:width" => $this->getWidth(),
                    "salsify:height" => $this->getHeight(),
                    "salsify:resource_type" => $this->getResourceType(),
                    "salsify:format" => $this->getFormat(),
                    "salsify:status" => $this->getStatus(),
                ];
                break;
        }

        foreach ($this->meta as $meta_key => $value) {
            $array[$meta_key] = $value;
        }

        return $array;
    }

    /**
     * @see https://help.salsify.com/help/digital-asset-crud-api
     *
     * CREATE an asset - POST https://app.salsify.com/api/v1/orgs/<org_ID>/digital_assets
     * READ an asset - GET https://app.salsify.com/api/v1/orgs/<org_ID>/digital_assets/<asset_id>
     * UPDATE an asset - PUT https://app.salsify.com/api/v1/orgs/<org_ID>/digital_assets/<asset_id>
     * DELETE an asset - DELETE https://app.salsify.com/api/v1/orgs/<org_ID>/digital_assets
     * REFRESH an asset - POST https://app.salsify.com/api/v1/orgs/<org_ID>/digital_assets/refresh
     */

    /**
     * @return \Psr\Http\Message\ResponseInterface ~ successful status codes: 201 create and 204 for update
     */
    public function create()
    {
        return $this->api->doRequest(
            'POST',
            'digital_assets',
            ['form_params' => $this->toArray('create')]
        );
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface ~ successful status code 204
     */
    public function delete()
    {
        return $this->api->doRequest(
            'DELETE',
            "digital_assets", [
            'form_params' => ['salsify:id' => [$this->getId()]]
        ]);
    }

    /**
     * @return array
     */
    public function get()
    {
        $response = $this->api->doRequest('GET', "digital_assets/{$this->getId()}");

        if ($response->getStatusCode() == 404) {
            return ['errors' => ['Sku not found in Salsify']];
        }

        $attributes = json_decode($response->getBody(), true);
        $this->setFromArray($attributes, true);
        return $this->toArray();
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface ~ successful status codes: 201 create and 204 for update
     */
    public function refresh()
    {
        return $this->api->doRequest(
            'POST',
            "digital_assets/refresh", [
            'form_params' => ['salsify:id' => [$this->getId()]]
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface ~ successful status codes: 201 create and 204 for update
     */
    public function update()
    {
        $data = $this->toArray('update');
        return $this->api->doRequest(
            'PUT',
            "digital_assets/{$this->getId()}", [
            'form_params' => $data
        ]);
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId(string $id): DigitalAsset
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): DigitalAsset
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $source_url
     * @return $this
     */
    public function setSourceUrl($source_url)
    {
        $this->source_url = $source_url;
        return $this;
    }

    /**
     * @param mixed $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @param string $name
     * @param string|mixed $value
     *
     * @return $this
     */
    public function setMeta(string $name, $value): DigitalAsset
    {
        $this->meta[$name] = $value;
        return $this;
    }
}