<?php
//declare(strict_types=1);

use LCI\Salsify\API;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    /** @var API */
    protected static $api;

    /**
     * Setup static properties when loading the test cases.
     */
    public static function setUpBeforeClass()
    {

    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass()
    {

    }

    /**
     * @param bool $new
     *
     * @return API
     */
    public static function getApiInstance($new = false)
    {
        if ($new || !is_object(self::$api)) {

            $api = new API(API_ORG_ID, API_TOKEN, API_BASE_URI);

            if (is_object($api)) {
                self::$api = $api;
            }
        }
        return self::$api;
    }

    /**
     * Set up the xPDO(modx) fixture for each test case.
     */
    protected function setUp()
    {

    }

    /**
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }
}
