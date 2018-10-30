<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 10/23/18
 * Time: 11:54 AM
 */

namespace LCI\Salsify\Helpers;

use IteratorAggregate;
use League\Csv\Reader;
use League\Csv\Statement;

trait Source
{
    protected $source_json = '';

    /** @var string  */
    protected $source_csv = '';

    /** @var array  */
    protected $flat_source = [];

    /**
     * @param string $source_csv
     * @return $this
     */
    public function loadSourceFromCsv(string $source_csv)
    {
        $this->source_csv = $source_csv;

        $this->loadFlatSourceFromCSV();

        return $this;
    }
    /**
     * @param string $source_json
     * @return $this
     */
    public function loadSourceFromJson(string $source_json)
    {
        // @TODO
        $this->source_json = $source_json;
        return $this;
    }

    /**
     * @return array
     */
    public function getRawArray()
    {
        return $this->flat_source;
    }

    protected function loadFlatSourceFromCSV()
    {
        $csv = Reader::createFromPath($this->source_csv, 'r');

        $index_map = $csv->fetchOne();

        $header_map = $this->getHeaderMap($index_map);

        $rows = $csv->getRecords();

        $count = 0;
        $this->flat_source = [];
        // Rows can share headers to need to make them arrays to not lose values
        foreach ($rows as $row) {
            if ($count++ == 0) {
                continue;
            }
            $this->flat_source[] = $this->mapRowToHeaders($row, $index_map, $header_map);
        }
    }

    /**
     * @param array $first_row
     * @return array
     */
    protected function getHeaderMap($first_row=[])
    {
        // name => type ~ single/array
        $map = [];

        foreach ($first_row as $count => $name) {
            $type = 'single';
            if (isset($map[$name])) {
                $type = 'array';
            }

            $map[$name] = $type;
        }

        return $map;
    }

    /**
     * @param array $row
     * @param array $index_map
     * @param array $header_map
     * @return array
     */
    protected function mapRowToHeaders(array $row, array $index_map, array $header_map)
    {
        $data = [];
        foreach ($row as $count => $value) {
            $header = $index_map[$count];

            if ($header_map[$header] == 'array') {
                if (!isset($data[$header])) {
                    $data[$header] = [];
                }

                if (empty($value)) {
                    continue;
                }
                $data[$header][] = $value;

            } else {
                $data[$header] = $value;

            }
        }

        return $data;
    }
}