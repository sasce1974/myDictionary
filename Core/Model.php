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
     * @throws \ReflectionException
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
    //protected static function getDB()
    protected function con()
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
     * @param array|null $column
     * @param string $output_as
     * @return array
     */
    function all(array $column = null, $output_as = 'class'){
        try {
            $column = $column ? implode(", ", $column) : "*";
            $q = "SELECT $column FROM $this->table";
            $query = $this->con->query($q);
            if($output_as == 'object') return $query->fetchAll(PDO::FETCH_OBJ);
            return $query->fetchAll(PDO::FETCH_CLASS, $this->model);
        }catch (PDOException $e){
            print $e->getMessage();
        }
    }


    /**
     * @param int $limit
     * @param string $output_as
     * @return array
     */
    function some($limit, $output_as = 'class'){
        try {
            $q = "SELECT * FROM $this->table LIMIT ?";
            $query = $this->con->prepare($q);
            $query->execute(array((int)$limit));
            if($output_as == 'object') return $query->fetchAll(PDO::FETCH_OBJ);
            return $query->fetchAll(PDO::FETCH_CLASS, $this->model);
        }catch (PDOException $e){
            print $e->getMessage();
        }
    }


    /**
     * Return instance of model by given ID
     *
     * @param $id
     * @return $this | false
     * @throws \Exception
     */
    function find($id){
        //check if column 'id' exist in the table
        $test_q = $this->con->query("SHOW COLUMNS FROM $this->table LIKE 'id'");
        if(!$test_q->rowCount()) throw new \Exception("Column 'id' not found in table $this->table");

        $q = "# noinspection SqlResolve @ column/\"id\" 
        SELECT * FROM $this->table WHERE id = ?";
        //$query = $this->con->query("SELECT * FROM $this->table WHERE id = $id");
        $query = $this->con->prepare($q);
        $query->execute(array($id));
        if ($query->rowCount() !== 1) return false;
        $query->setFetchMode(PDO::FETCH_CLASS, $this->model);
        $model = $query->fetch();
        $this->init($model);

        return $this;
    }

    /**
     * Returns array of model class objects or just objects
     *
     * @param $item [Model (table) column name (e.g. 'user_id')]
     * @param $value [column value]
     * @param string $output_as [optional, can be 'class' or 'object'. Default is 'class']
     * @return array
     * @throws \Exception
     */
    function where($item, $value, $output_as = 'class'){

        $item = filter_var($item, FILTER_SANITIZE_STRING);
        $test_q = $this->con->query("SHOW COLUMNS FROM $this->table LIKE '$item'");
        if(!$test_q->rowCount()) throw new \Exception("Column $item not found in table $this->table");

        $q = "SELECT * FROM $this->table WHERE $item = ? LIMIT 150";
        $query = $this->con->prepare($q);
        $query->execute(array($value));
        if($output_as == 'object') return $query->fetchAll(PDO::FETCH_OBJ);
        return $query->fetchAll(PDO::FETCH_CLASS, $this->model);
    }


    function countWhere($item, $value){

        $item = filter_var($item, FILTER_SANITIZE_STRING);
        $test_q = $this->con->query("SHOW COLUMNS FROM $this->table LIKE '$item'");
        if(!$test_q->rowCount()) throw new \Exception("Column $item not found in table $this->table");

        $q = "SELECT COUNT(id) FROM $this->table WHERE $item = ?";
        $query = $this->con->prepare($q);
        $query->execute(array($value));
        return $query->fetchColumn();
    }


    public function toArray(){
        return (array) $this;
    }

    public function count(){
        return count($this);
    }

    public function first(){
        return $this[0];
    }

    public function last(){
        return $this[count($this)-1];
    }

    function __destruct()
    {
        $this->con = null;
    }


    /**
     * Handle dynamic static method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    /*public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }*/
}