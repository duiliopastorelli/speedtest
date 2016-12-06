<?php
/**
 * Created by Danilo Mezgec
 * Date: 03/12/16
 */

namespace duiliopastorelli\SpeedPerformance;

use Exception;
class MyException extends Exception { }

class SpeedPerformance
{

    function __construct(){

        $this->tester = 'wpt';
        $this->format = 'json';
    }



    public function wptSendRequest($url, $email=null, $key){

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
            $result = file_get_contents('http://www.webpagetest.org/runtest.php', false, $context);

            if ($result === FALSE) {
                throw new Exception('Error in the api consumption');
            }

        } catch (Exception $e){
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        };

        var_dump($result);

        return $result;
    }
}
