<?php

require_once 'PHPUnit/Framework.php';

require_once('ClientTest.php');
require_once('SecurityTest.php');

class Package_AllTests extends PHPUnit_Framework_TestSuite
{
    /**
     * Configures the test suite.
     * @return Package_AllTests
     */
    public static function suite()
    {
        $suite = new Package_AllTests('Package');
        $suite->addTestSuite('ClientTest');
        $suite->addTestSuite('SecurityTest');

        return $suite;
    } // end suite();

    /**
     * Executed before the test suite.
     */
    protected function setUp()
    {
    } 

    /**
     * Finishes the testing suite
     */
    protected function tearDown()
    {
        // some finalization code
    }

} 