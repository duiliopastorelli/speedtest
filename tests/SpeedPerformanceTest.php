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

        $wptConfigTest = new SpeedPerformance\Settings();
        $this->assertFalse($wptConfigTest->wptIsProperlySet);
        $wptConfigTest->getSettings('mocks/testConfig.json');
        $this->assertTrue($wptConfigTest->wptIsProperlySet);

        $wptConfigTestWrong = new SpeedPerformance\Settings();
        $wptConfigTestWrong->getSettings('mocks/testConfigBad.json');
        $this->assertFalse($wptConfigTestWrong->wptIsProperlySet);
    }

    /**
     * @test
     */
    public function wptTestRequestWorksProperly() {
        /**
         * Given an url
         * request a test from WebPageTest
         * return the test result url
         */

        $url = 'https://www.facebook.com';
        $wptConfigRequestTest = new SpeedPerformance\Settings();
        $wptConfigRequestTest->getSettings("./config.json");
        $wptRequestTest = new SpeedPerformance\SpeedPerformance();
        $this->assertInternalType('array',$wptRequestTest->wptSendRequest($url,$wptConfigRequestTest->wptKey));
    }

    /**
     * test
     */
    public function footestName(){
        /**
         * Given an test result url
         * retrieve the JSON and check --> statusText: "Test Complete"
         * if false wait and retrieve the JSON again for max 10 times.
         */

        $this->assertEquals(true,false);
    }




    private function wptTests() {

        $tO = array(
            'key' => 'A.204365e99f80e5e48161300e10d16962',
            'url' => 'http://www.facebook.com',
            'email' => 'mezgec.danilo@gmail.com'
        );

        $speedTest = new SpeedPerformance('foo');

        /**
         * As an authorized user
         * given an url and an feedback email
         * retrieve a json response with status code of 200 from WPT
         * return the test url or catch an exception
         */

        $returnedJson = $speedTest->wptSendRequest($tO['url'], $tO['email'], $tO['key']);
    }

}