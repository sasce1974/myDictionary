<?php


namespace App;


class Config
{
    private static $instance = null;

    private static $config = [
        'db_host'=>'localhost',
        'db_name'=>'dictionary',
        'db_user'=>'user',
        'db_pass'=>'secret',
        'show_errors'=>true,
        // here add other configurations
        'app.url'=>'http://mydictionary.test'
    ];


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
        if(isset(self::$config[$index])){
            return self::$config[$index];
        }else{
            throw new \Exception("Configuration $index not found", 404);
        }
    }
}
