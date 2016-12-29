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

        $settings = new Settings();
        $settings->getSettings($appStatus);

        $this->tester = 'wpt';
        $this->format = 'json';
        $this->settings = $settings;
    }

    /**
     * @return mixed
     *
     * Send a test request to WPT and return a JSON with the server response
     */
    public function wptSendRequest(){

        $logger = new Logger('wptRequest');

        $data = array(
            'k'         => $this->settings->wptKey,
            'private'   => 1,
            'f'         => $this->format,
            'notify'    => $this->settings->wptEmail,
            'url'       => $this->settings->wptUrl
        );

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );

        $context  = stream_context_create($options);

        try {
            $response = file_get_contents('http://www.webpagetest.org/runtest.php', false, $context);
            $wptRequestJson = json_decode($response, true);

            if ($response === FALSE) {
                throw new Exception('Error in the api consumption: response is empty.');
            } elseif ($wptRequestJson['statusCode'] != 200){
                throw new Exception('Error in the api consumption, the server didn\'t respond with a 200 but with: ' . $wptRequestJson['statusCode']);
            }
        } catch (Exception $e){
            $logger->pushHandler(new StreamHandler(__DIR__.'/speedPerformance.log', Logger::ERROR));
            $logger->addError($e->getMessage() . " on file: " . $e->getFile() . " and line: " . $e->getLine());
        };

        return $wptRequestJson;
    }


    /**
     * @param $jsonResponse
     * @return array
     *
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

    public function getWptTestData($request){

        $logger = new Logger('wptGetTestData');

        try {
            if (isset($request) && gettype($request) == 'array'){
                $testData = file_get_contents($request['data']['jsonUrl'], false, $context);
//                return array();
            } else {
                throw new Exception('getWptTestData needs an array that comes from the wptSendRequest. ' .
                gettype($request) . ' given.');
            }
        } catch (Exception $e){
            $logger->pushHandler(new StreamHandler(__DIR__.'/speedPerformance.log', Logger::ERROR));
            $logger->addError($e->getMessage() . " on file: " . $e->getFile() . " and line: " . $e->getLine());
        };
    }


    public function runTest(){

        try {
            $settings = new Settings();
            $this->wptSendRequest();
        } catch (Exception $e){

        }
    }
}
