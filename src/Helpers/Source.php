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


    protected function loadFlatSourceFromCSV()
    {
        $csv = Reader::createFromPath($this->source_csv, 'r');

        $csv->setHeaderOffset(0);

        $header = $csv->getHeader(); //returns the CSV header record
        /** @var IteratorAggregate $records */
        //$records = $csv->getRecords(); //returns all the CSV records as an Iterator object

        $this->flat_source = (new Statement())->process($csv);
    }
}