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
     * test
     */
    public function wptConfigLoadedOnceProperly() {
        /**
         * Given a configuration file
         * check if it's already loaded in memory
         * if not loaded in memory parse the objects and extract the various info
         * return the respective objects and associated arrays
         */

        $wptConfig = new SpeedPerformance\Settings();
        $this->assertFalse($wptConfig->wptIsProperlySet);
        $wptConfig->getSettings('mocks/testConfig.json');
        $this->assertTrue($wptConfig->wptIsProperlySet);

        $wptConfigWrong = new SpeedPerformance\Settings();
        $wptConfigWrong->getSettings('mocks/testConfigBad.json');
        $this->assertFalse($wptConfigWrong->wptIsProperlySet);
    }

    /**
     * test
     */
    public function wptTestRequestWorksProperly() {
        /**
         * Given an url
         * request a test from WebPageTest
         * return the test result url
         */

        $url = 'https://www.facebook.com';
        $wptConfigRequest = new SpeedPerformance\Settings();
        $wptConfigRequest->getSettings("./config.json");
        $wptRequest = new SpeedPerformance\SpeedPerformance();
        $this->assertEquals('Ok',$wptRequest->wptSendRequest($url,$wptConfigRequest->wptKey)['statusText']);
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

        $url = 'https://www.facebook.com';
        $wptConfig = new SpeedPerformance\Settings();
        $wptConfig->getSettings('./config.json');
        $wptData = new SpeedPerformance\SpeedPerformance();
        $request = $wptData->wptSendRequest($url,$wptConfig->wptKey);
        $this->assertInternalType('array',$wptData->getWptTestData($request));
    }
}
