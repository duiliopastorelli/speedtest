<?php
/**
 * Created by Danilo Mezgec
 * Date: 30/12/2016
 */

namespace duiliopastorelli\SpeedPerformance;

use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SpeedPerformanceItem
{
    private $url = null;
    public $status = null;
    public $testResultData = null;


    /**
     * SpeedPerformanceItem constructor.
     * @param $settings
     * @param $url
     * The settings are provided by the Settings class.
     * The url is passed as a 2nd parameter to help to manage the queue.
     */
    function __construct($settings, $url)
    {
        $this->settings = $settings;
        $this->url = $url;
        $this->format = 'json';
    }


    /**
     * @return mixed|null
     * This function request the test and return the response from the WPT server as a JSON object.
     */
    public function wptGetTest(){

        try {
            $logger = new Logger('wptRequest');
            $wptRequestJson = null;

            $data = array(
                'k'         => $this->settings->getWptKey(),
                'private'   => 1,
                'f'         => $this->format,
                'notify'    => $this->settings->getWptEmail(),
                'url'       => $this->url
            );

            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                )
            );

            $context  = stream_context_create($options);

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
}