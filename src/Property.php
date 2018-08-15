<?php
/**
 * Created by PhpStorm.
 * User: jgulledge
 * Date: 7/21/2017
 * Time: 2:27 PM
 */

namespace LCI\Salsify;

use LCI\Salsify\Helpers\Syntax;

class Property
{
    use Syntax;

    /** @var API  */
    protected $api;

    /** @var bool  */
    protected $property_exists = false;

    /** @var string */
    protected $id;

    /** @var null|string  */
    protected $name = null;

    /** @var string  */
    protected $data_type = 'string';

    /** @var null|string */
    protected $help_text = null;

    /** @var null */
    protected $position = null;

    /** @var string */
    protected $attribute_group;

    /**
     * Property constructor.
     * @param API $api
     * @param string $id
     */
    public function __construct(API $api, string $id)
    {
        $this->api = $api;
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function explainDataTypes()
    {
        return [
            'string' => '(default) plain text, accepts alpha numeric and special characters, case sensitive.',
            'link' => 'a url',
            'html' => 'HTML',
            'rich_text' => 'accepts alpha-numeric and special characters with formatting, case sensitive.',
            'enumerated' => 'an enumerated set of attribute values. Plain text, alpha numeric characters, hierarchical, fixed set, case sensitive.',
            'number' => 'Plain, numeric characters only',
            'date' => 'yyyy-mm-dd format',
            'boolean' => 'Accepts y, yes, n, no, true, false, not case sensitive. Exports to JSON format as true or false.',
            'digital_asset' => 'Digital Asset'
        ];
    }

    /**
     * @param array $data
     * @return $this
     */
    public function fromArray($data)
    {
        foreach ($data as $key => $value) {
            if (empty($key)) {
                continue;
            }

            $method_name = 'set'.$this->makeStudyCase($key);

            if (method_exists($this, $method_name)) {
                $this->$method_name($value);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getAttributeGroup()
    {
        return $this->attribute_group;
    }

    /**
     * @return string
     */
    public function getDataType()
    {
        return $this->data_type;
    }

    /**
     * @return null|string
     */
    public function getHelpText()
    {
        return $this->help_text;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function isPropertyExists()//: bool
    {
        return $this->property_exists;
    }

    /**
     * @param bool $property_exists
     *
     * @return $this
     */
    public function setPropertyExists($property_exists)
    {
        $this->property_exists = $property_exists;
        return $this;
    }

    /**
     * @param string $attribute_group
     * @return $this
     */
    public function setAttributeGroup($attribute_group)
    {
        $this->attribute_group = $attribute_group;
        return $this;
    }
    /**
     * @param string $data_type - see $this->explainDataTypes()
     *
     * @return $this
     */
    public function setDataType($data_type)
    {
        // see $this->explainDataTypes()
        switch($data_type) {
            case 'link':
                break;
            case 'html':
                break;
            case 'rich_text':
                break;
            case 'enumerated':
                break;
            case 'number':
                break;
            case 'date':
                break;
            case 'boolean':
                break;
            case 'digital_asset':
                break;
            default:
                $data_type = 'string';

        }
        $this->data_type = $data_type;

        return $this;
    }

    /**
     * @param mixed $help_text
     *
     * @return $this
     */
    public function setHelpText($help_text)
    {
        $this->help_text = $help_text;
        return $this;
    }

    /**
     * @param mixed $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = (int)$position;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        // proper salsify array:
        return [
            'salsify:id' => $this->id,
            'salsify:name' => (!empty($this->name) ? $this->name : $this->id),
            'salsify:data_type' => $this->data_type,
            'salsify:position' => $this->position,
            'salsify:help_text' => $this->help_text,
            'salsify:attribute_group' => $this->attribute_group,
            /*
            'salsify:entity_types' => [],
            'salsify:is_facetable' => true,
            'salsify:manage_permissions' => null,
            'salsify:read_permissions' => null,
            'salsify:hidden_permissions' => null,
            */
        ];
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface ~ successful status codes: 201 create and 204 for update
     */
    public function create()
    {
        return $this->api->doRequest('POST', 'properties', ['form_params' => $this->toArray()]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface ~ successful status code 204
     */
    public function delete()
    {
        return $this->api->doRequest('DELETE', "properties/{$this->getId()}");
    }

    /**
     * @return array
     */
    public function load()
    {
        $query = [
            'query' => [
                'id' => $this->getId()
            ]
        ];

        $response = $this->api->doRequest('GET', "properties/{$this->getId()}", $query);

        $attributes = [];
        if (is_object($response) && $response->getStatusCode() == '200') {
            $this->setPropertyExists(true);
            $temp = json_decode($response->getBody(), true);
            foreach ($temp as $key => $value) {
                $attributes[str_replace('salsify:', '', $key)] = $value;
            }
            $this->fromArray($attributes);
        }

        return $attributes;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface ~ successful status codes: 201 create and 204 for update
     */
    public function save()
    {
        if (!$this->property_exists) {
            return $this->create();
        } else {
            return $this->update();
        }
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface ~ successful status codes: 201 create and 204 for update
     */
    public function update()
    {
        $data = $this->toArray();
        unset($data['id']);
        return $this->api->doRequest(
            'PUT',
            "properties/{$this->getId()}", [
            'form_params' => $data
        ]);
    }

    /**
     * Internal Salsify route ~ unstable for public use
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function getPropertyValues()
    {
        $query = [
            'query' => [
                'id' => $this->getId()
            ]
        ];

        return $this->api->doRequest('GET', "properties/{$this->getId()}/enumerated_values", $query);
    }
}
