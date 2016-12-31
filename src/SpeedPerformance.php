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

        $logger = new Logger('wptCheckResponseStatus');
        $code = null;
        $status = null;

        try {
            if(!isset($jsonResponse['statusCode'])){
                throw new Exception('Error in getting the status code from the server response: ' . $jsonResponse['statusCode']);
            } else {
                $code = $jsonResponse['statusCode'];
            }

            if(!isset($jsonResponse['statusText'])){
                throw new Exception('Error in getting the status message from the server response: ' . $jsonResponse['statusText']);
            }
        } catch (Exception $e){
            $logger->pushHandler(new StreamHandler(__DIR__.'/speedPerformance.log', Logger::ERROR));
            $logger->addError($e->getMessage() . " on file: " . $e->getFile() . " and line: " . $e->getLine());
        }

        return compact($code, $status);
    }


    public function wptQueueManagement(){

        $logger = new Logger('wptGetTestData');


        $urlStatus = array();

        foreach ($this->settings->wptUrl as $url){
            $urlStatus[$url][] = false;

            var_dump($urlStatus);
        }

        try {
            $count = 0;

            for ($i=0; $i<5; $i++){

                foreach ($this->settings->wptUrl as $url){

                    if($urlStatus[$url][false]){
                        $response = $this->wptSendRequest();
                        printf('is false');

                        if($this->checkResponseStatus($response)[0] == 200){
                            $this->$urlStatus[$url][true];
                            printf('became true');
                            var_dump($urlStatus);
                            $count ++;
                        }
                    }
                }

                if($count == count($this->settings->wptUrl)){
                    break;
                } else {
                    printf('waiting 1s');
                    usleep(1000000);
                }
            }
        } catch (Exception $e){
            $logger->pushHandler(new StreamHandler(__DIR__.'/speedPerformance.log', Logger::ERROR));
            $logger->addError($e->getMessage() . " on file: " . $e->getFile() . " and line: " . $e->getLine());
        };

        return $response;
    }
}
