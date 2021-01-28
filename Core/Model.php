<?php


namespace Core;

use App\Config;
use App\Interfaces\iModel;
use PDO;
use PDOException;

abstract class Model implements iModel, \Countable
{
    /**
     * contains table name.
     *
     * Change this if table name does not follow the convention Model name -> table name + (s)
     *
     * @var string
     */
    protected $table = "";

    protected $model;
    protected $con = null;



    /**
     * Model constructor.
     *
     * Create table name, then Initiate object of this model if object data is provided in argument
     *
     * @param null $object
     */
    public function __construct($object = null)
    {
        if($this->table == "") {
            $this->table = lcfirst((new \ReflectionClass(get_class($this)))->getShortName()) . "s";
            //$this->table = lcfirst(get_class($this)) . 's'; // table name as Model class name + 's'
        }
        $this->model = get_class($this);
        $this->con = DB_connection::getCon();
        if($object) $this->init($object);

    }


    /**
     *
     * Models use this function to initiate its instance by given object data ($model)
     *
     * @param $model
     */
    public function init($model){}



    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB()
    {
        return DB_connection::getCon();
    }


    /**
     *
     * Get collection of all data from the table for particular model
     *
     * Each model object data from database is passed as argument to Model constructor
     * processed in the init method, then collected in $this->collection array
     *
     * @return array
     */
    function all(){
        try {
            $q = "SELECT * FROM $this->table";
            $query = $this->con->query($q);

            return $query->fetchAll(PDO::FETCH_CLASS, $this->model);
        }catch (PDOException $e){
            print $e->getMessage();
        }
    }


    function some($limit){
        try {
            $q = "SELECT * FROM $this->table LIMIT ?";
            $query = $this->con->prepare($q);
            $query->execute(array($limit));

            return $query->fetchAll(PDO::FETCH_CLASS, $this->model);
        }catch (PDOException $e){
            print $e->getMessage();
        }
    }


    /**
     * Return instance of model by given ID
     *
     * @param $id
     * @return $this
     */
    function find($id){
        $q = "SELECT * FROM $this->table WHERE id = ?";
        //$query = $this->con->query("SELECT * FROM $this->table WHERE id = $id");
        $query = $this->con->prepare($q);
        $query->execute(array($id));
        if($query->rowCount() !== 1) return false;
        $query->setFetchMode(PDO::FETCH_CLASS, $this->model);
        $model = $query->fetch();
        $this->init($model);
        //var_dump($this); exit();
        return $this;
    }

    function where($item, $value){
        $item = filter_var($item, FILTER_SANITIZE_STRING);
        $q = "SELECT * FROM $this->table WHERE $item = ? LIMIT 150";
        $query = $this->con->prepare($q);
        $query->execute(array($value));
        return $query->fetchAll(PDO::FETCH_CLASS, $this->model);
    }

    public function toArray(){
        return (array) $this;
    }

    public function count(){
        return count($this);
    }

    function __destruct()
    {
        $this->con = null;
    }
}