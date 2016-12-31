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
    static private $settingsInstantiated = 0;
    private $wptIsProperlySet = null;
    private $rawConfig = null;
    private $wptKey = null;
    private $wptEmail = null;
    private $wptUrl = null;


    public function __construct($appStatus)
    {

    }


    /**
     * @param null $appStatus
     * @return Settings
     * Return the settings needed for WPT
     */
    public static function getSettings($appStatus=null) {

        $logger = new Logger('settings');

        //Read the configuration file

        try {
            $settingsLoaded = new Settings($appStatus);

            switch ($appStatus){
                case "dist":
                    $configFilePath = "./config.json";
                    break;

                case "test":
                    $configFilePath = "./mocks/testConfig.json";
                    break;

                case "testBadConfig":
                    $configFilePath = "./mocks/testConfigBad.json";
                    break;

                default:
                    $configFilePath = "./config.json";
                    break;
            }

            $settingsLoaded->rawConfig = file_get_contents($configFilePath);

            //Parse the file and obtain the JSON objects
            $configJson = json_decode($settingsLoaded->rawConfig, true);

            $wptConfig = $configJson['wpt'];

            if(isset($wptConfig)){
                if (isset($wptConfig['key']) && is_string($wptConfig['key'])){
                    $settingsLoaded->wptKey = $wptConfig['key'];
                } else {
                    $settingsLoaded->wptIsProperlySet = false;

                    throw new Exception('The key for Web Page Test must be configured and be a string! It is: ' . gettype($wptConfig['key']));
                }

                if (isset($wptConfig['email'])){
                    $settingsLoaded->wptEmail = $wptConfig['email'];
                } else {
                    $settingsLoaded->wptEmail = '';
                }

                if(isset($wptConfig['url'])){
                    $settingsLoaded->wptUrl = $wptConfig['url'];
                } else {
                    $settingsLoaded->wptIsProperlySet = false;

                    throw new Exception('The url for the Web Page Test must be declared into the config file!');
                }

                $settingsLoaded->wptIsProperlySet = true;
                self::$settingsInstantiated ++;
            }
        } catch (Exception $e){
            $logger->pushHandler(new StreamHandler(__DIR__.'/speedPerformance.log', Logger::ERROR));
            $logger->addError($e->getMessage() . " on file: " . $e->getFile() . " and line: " . $e->getLine());
        }

        return $settingsLoaded;
    }


    public function getWptIsProperlySet(){
        return $this->wptIsProperlySet;
    }


    public function getWptKey(){
        return $this->wptKey;
    }


    public function getWptEmail(){
        return $this->wptEmail;
    }


    public function getWptUrl(){
        return $this->wptUrl;
    }
}