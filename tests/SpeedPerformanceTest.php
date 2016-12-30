<?php
/**
 * Created by Danilo Mezgec.
 * Date: 03/12/16
 */

require 'vendor/autoload.php';
require 'src/SpeedPerformance.php';
require 'src/Settings.php';

use duiliopastorelli\SpeedPerformance as SpeedPerformance;

class SpeedPerformanceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function wptConfigLoadedOnceProperly() {
        /**
         * Given a configuration file
         * check if it's already loaded in memory
         * if not loaded in memory parse the objects and extract the various info
         * return the respective objects and associated arrays
         */

        $this->assertEquals(null, SpeedPerformance\Settings::getWptIsProperlySet());

        SpeedPerformance\Settings::getSettings("test");
        $this->assertTrue(SpeedPerformance\Settings::getWptIsProperlySet());

        SpeedPerformance\Settings::resetConfigStatus();
        SpeedPerformance\Settings::getSettings("testBadConfig");
        $this->assertFalse(SpeedPerformance\Settings::getWptIsProperlySet());
    }

    /**
     * test
     */
    public function wptTestRequestWorksProperly() {
        /**
         * Given an url
         * request a test from WebPageTest
         * return the test result url
         *
         * Note: this test requires a working internet connection and a working config file (for the key)
         */

        $wptRequest = new SpeedPerformance\SpeedPerformance();
        $wptRequest->settings->wptUrl = "https://www.facebook.com";
        $this->assertEquals('Ok',$wptRequest->wptSendRequest()['statusText']);
    }

    /**
     * test
     */
    public function checkResponseStatus(){
        /**
         * Given a request response
         * return the statusCode and the satusText;
         */

        $jsonFile100 = json_decode(file_get_contents("./mocks/incompleteTest.json"), true);
        $this->assertEquals(100,$jsonFile100['statusCode']);
        $this->assertEquals("Test Started 17 seconds ago",$jsonFile100["statusText"]);

        $jsonFile200 = json_decode(file_get_contents("./mocks/completeTest.json"), true);
        $this->assertEquals(200,$jsonFile200['statusCode']);
        $this->assertEquals("Test Complete",$jsonFile200["statusText"]);
    }

    /**
     * test
     */
    public function wptQueueManagement(){
        /**
         * Send the first request
         * return the result or await and try again if the result it's not ready
         */

        $wptRequest = new SpeedPerformance\SpeedPerformance();
        $wptRequest->wptQueueManagement();
    }

    /**
     * test
     */
    public function getWptTestData(){
        /**
         * Given an test result url
         * retrieve the JSON and check --> statusText: "Test Complete"
         * if false wait and retrieve the JSON again for max 10 times.
         */

        $wptConfig = new SpeedPerformance\Settings();
        $wptConfig->getSettings('./config.json');
        $wptData = new SpeedPerformance\SpeedPerformance();
        $request = $wptData->wptSendRequest();
        $this->assertInternalType('array',$wptData->getWptTestData($request));
    }
}
