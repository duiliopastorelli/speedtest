<?php
/**
 * Created by Danilo Mezgec.
 * Date: 05/12/16
 */

namespace duiliopastorelli\SpeedPerformance;

use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Settings
{

    function __construct()
    {
        $this->wptIsAlreadySet = false;
        $this->wptIsProperlySet = false;
        $this->rawConfig = null;
        $this->wptKey = null;
        $this->wptEmail = null;
    }

    public function getSettings($configFilePath) {

        $logger = new Logger('settings');

        //Read the configuration file
        if (!$this->wptIsAlreadySet){
            $this->rawConfig = file_get_contents($configFilePath);
        }

        //Parse the file and obtain the JSON objects
        $configJson = json_decode($this->rawConfig, true);

        $wptConfig = $configJson['wpt'];

        try {
            if(isset($wptConfig)){
                if (isset($wptConfig['key']) && is_string($wptConfig['key'])){
                    $this->wptKey = $wptConfig['key'];
                } else {
                    throw new Exception('The key for Web Page Test must be configured and be a string!');
                }

                if (isset($wptConfig['email'])){
                    $this->wptEmail = $wptConfig['email'];
                }

                $this->wptIsProperlySet = true;
            }
        } catch (Exception $e){
            $logger->pushHandler(new StreamHandler(__DIR__.'/speedPerformance.log', Logger::ERROR));
            $logger->addError($e->getMessage());
        }

        return $configJson;
    }
}