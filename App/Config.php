<?php


namespace App;


class Config
{
    private static $instance = null;

    private static $config = [];


    private function __construct(){}
    private function __clone(){}

    private static function getInstance(){
        if(self::$instance == null){
            $classname = __CLASS__;
            self::$instance = new $classname;
        }
        return self::$instance;
    }

    public static function getConfig($index){

        self::$config = self::getEnv();

        if(isset(self::$config[$index])){
            return self::$config[$index];
        }else{
            throw new \Exception("Configuration $index not found", 404);
        }
    }

    private static function getEnv(){
        try {
            $config_array = [];
            if (file_exists('../.env')) {
                $env = file_get_contents('../.env');

                $rows = explode("\n", $env);
                foreach ($rows as $row) {
                    $row = explode("=", $row);
                    $config_array[trim($row[0])] = trim($row[1]);
                }
            } else {
                throw new \Exception("File '.env' not found. Please make sure you have 
            set up the environment file - copy '.env.example', rename to '.env', open it and edit
            according your settings.", 404);
            }
            return $config_array;
        }catch (\Exception $e){
            print $e->getMessage();
        }
    }
}
