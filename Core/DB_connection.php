<?php


namespace Core;
use App\Config;
use PDO, PDOException;

class DB_connection
{
    private static $instance = null;

    private $con;

    private function __construct(){}
    private function __clone(){}

    private static function getInstance(){
        if(self::$instance == null){
            $classname = __CLASS__;
            self::$instance = new $classname;
        }
        return self::$instance;
    }

    public static function getCon(){
        try{
            $db = self::getInstance();
            $db->con = new PDO('mysql:host=' . Config::getConfig('db_host') .
            ';dbname=' . Config::getConfig('db_name'),
            Config::getConfig('db_user'),
            Config::getConfig('db_pass'));
            $db->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->con->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);//useful for LIMIT with prepared statement
            return $db->con;
        }catch (PDOException $e){
            print $e->getMessage();
        }
    }
}