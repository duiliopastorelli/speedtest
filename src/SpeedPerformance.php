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
    function __construct(){

        $this->tester = 'wpt';
        $this->format = 'json';
    }

    public function wptSendRequest($url, $key, $email=null){

        $logger = new Logger('wptRequest');

        $data = array(
            'k'   => $key,
            'private' => 1,
            'f'   => $this->format,
            'notify' => $email,
            'url' => $url
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
                throw new Exception('Error in the api consumption: response is emplty.');
            } elseif ($wptRequestJson['statusCode'] != 200){
                throw new Exception('Error in the api consumption, the server didn\'t respond with a 200 but with: ' . $wptRequestJson['statusCode']);
            } else {
                return $wptRequestJson;
            }
        } catch (Exception $e){
            $logger->pushHandler(new StreamHandler(__DIR__.'/speedPerformance.log', Logger::ERROR));
            $logger->addError($e->getMessage());
        };
    }
}
