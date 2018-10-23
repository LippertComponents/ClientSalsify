<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 10/23/18
 * Time: 11:15 AM
 */

namespace LCI\Salsify\Helpers;


class PropertyValues
{
    use Source;

    /**
     * @param string $attribute_id
     * @return array
     */
    public function getPropertyValues(string $attribute_id)
    {
        $values = [];
        foreach ($this->flat_source as $row) {
            if ($row['salsify:attribute_id'] == $attribute_id) {
                $values[$row['salsify:id']] = $row;
            }
        }

        return $values;
    }

    /**
     * @param string $attribute_id
     * @return array
     */
    public function getPropertyValuesNested(string $attribute_id)
    {
        return $this->getChildValues($attribute_id, '');
    }

    /**
     * @param string $attribute_id
     * @param string $parent_id
     * @return array
     */
    protected function getChildValues(string $attribute_id, string $parent_id)
    {
        $names = [];
        $temp_values = [];
        $values = [];
        foreach ($this->flat_source as $row) {
            if ($row['salsify:attribute_id'] == $attribute_id && $row['salsify:parent_id'] == $parent_id) {
                $names[] = $row['salsify:name'];
                $temp_values[$row['salsify:name']] = $row;
            }
        }

        sort($names);
        foreach ($names as $name) {
            $values[$name] = [
                'item' => $temp_values[$name],
                'children' => $this->getChildValues($attribute_id, $temp_values[$name]['salsify:id'])
            ];
        }

        return $values;
    }
}