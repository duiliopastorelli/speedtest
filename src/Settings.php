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
    static private $wptIsAlreadySet = null;
    static private $wptIsProperlySet = null;
    static private $rawConfig = null;
    static private $wptKey = null;
    static private $wptEmail = null;
    static private $wptUrl = null;


    public function __construct()
    {
        self::$wptIsAlreadySet = false;
        self::$wptIsProperlySet = false;
        self::$rawConfig = null;
        self::$wptKey = null;
        self::$wptEmail = null;
        self::$wptUrl = null;
    }


    public static function resetConfigStatus(){
        self::$wptIsAlreadySet = false;
        self::$wptIsProperlySet = false;
    }

    /**
     * @return bool|null
     * Check if the configuration is properly set for the minimum WPT requirements
     */
    public static function getWptIsProperlySet(){

        return self::$wptIsProperlySet;
    }


    /**
     * @param null $appStatus
     * @return mixed
     * Return the settings needed for WPT
     */
    public static function getSettings($appStatus=null) {

        $logger = new Logger('settings');

        //Read the configuration file

        try {
            if (!self::$wptIsAlreadySet) {
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

                self::$rawConfig = file_get_contents($configFilePath);
            }

            //Parse the file and obtain the JSON objects
            $configJson = json_decode(self::$rawConfig, true);

            $wptConfig = $configJson['wpt'];

            if(isset($wptConfig)){
                if (isset($wptConfig['key']) && is_string($wptConfig['key'])){
                    self::$wptKey = $wptConfig['key'];
                } else {
                    throw new Exception('The key for Web Page Test must be configured and be a string! It is: ' . gettype($wptConfig['key']));
                }

                if (isset($wptConfig['email'])){
                    self::$wptEmail = $wptConfig['email'];
                } else {
                    self::$wptEmail = '';
                }

                if(isset($wptConfig['url'])){
                    self::$wptUrl = $wptConfig['url'];
                } else {
                    throw new Exception('The url for the Web Page Test must be declared into the config file!');
                }

                self::$wptIsProperlySet = true;
            } else {
                self::$wptIsProperlySet = false;
            }
        } catch (Exception $e){
            $logger->pushHandler(new StreamHandler(__DIR__.'/speedPerformance.log', Logger::ERROR));
            $logger->addError($e->getMessage() . " on file: " . $e->getFile() . " and line: " . $e->getLine());
        }

        return $configJson;
    }
}