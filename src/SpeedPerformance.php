<?php
/**
 * Created by Danilo Mezgec
 * Date: 03/12/16
 */

namespace duiliopastorelli\SpeedPerformance;

use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SpeedPerformance
{
    function __construct($appStatus=null){

        $settings = Settings::getSettings($appStatus);

        $this->tester = 'wpt';
        $this->settings = $settings;
    }


    public static function runTests($appStatus){
        $tests = new SpeedPerformance($appStatus);

        //todo Run the queue system
    }


    /**
     * @param $settings
     * @param $url
     * @return mixed|null
     * Send the single request for a single test. It is used by the queue management.
     */
    public function wptSendRequest($settings, $url){
        $request = new SpeedPerformanceItem($settings, $url);

        return $request->wptGetTest();
    }


    /**
     * @param $jsonResponse
     * @return array
     * Check the status code and the status message of a JSON response and returns an array just with these data.
     */
    public function checkResponseStatus($jsonResponse){

        try {
            $logger = new Logger('wptCheckResponseStatus');
            $code = null;
            $status = null;

            if(!isset($jsonResponse['statusCode'])){
                throw new Exception('Error in getting the status code from the server response: ' . $jsonResponse['statusCode']);
            } else {
                $code = $jsonResponse['statusCode'];
            }

            if(!isset($jsonResponse['statusText'])){
                throw new Exception('Error in getting the status message from the server response: ' . $jsonResponse['statusText']);
            } else {
                $status = $jsonResponse['statusText'];
            }
        } catch (Exception $e){
            $logger->pushHandler(new StreamHandler(__DIR__.'/speedPerformance.log', Logger::ERROR));
            $logger->addError($e->getMessage() . " on file: " . $e->getFile() . " and line: " . $e->getLine());
        }

        return compact($code, $status);
    }


    public function wptQueueManagement(){
        /**
         * Given a setting object with an array of urls
         * Run at least on test for every url
         * Retry until get !=100
         */

        try {
            $logger = new Logger('wptGetTestData');
            $tests = null;

            $urlsInQueue = $this->settings->getWptUrl();

            foreach ($urlsInQueue as $item) {

                //Instantiate a new item objects
                $tests[] = new SpeedPerformanceItem($this->settings, $item);
            }

            while (count($urlsInQueue) > 0){

                if(count($urlsInQueue) <= 10){
                    $waitTime = 20/count($urlsInQueue);
                } else {
                    $waitTime = 1;
                }

                foreach ($tests as $test) {

                    //Run the test over the targeted url
                    $result = $test->wptGetTest();

                    //check if the code returned is 100, 200 and manage the errors
                    if ($this->checkResponseStatus($test) == 200){

                        $test->testResultData = $result;
                        unset($tests[$test]);

                    } elseif($this->checkResponseStatus($test) != 100) {
                        throw new Exception('Error in getting the status code from the server response: ' . $result['statusCode']);
                    }

                    sleep($waitTime);
                }

                //add the response object to an array of objects ready to be returned
            }
        } catch (Exception $e){
            $logger->pushHandler(new StreamHandler(__DIR__.'/speedPerformance.log', Logger::ERROR));
            $logger->addError($e->getMessage() . " on file: " . $e->getFile() . " and line: " . $e->getLine());
        };

        return $result;
    }
}
