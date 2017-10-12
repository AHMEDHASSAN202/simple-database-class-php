<?php



class DB {

    /**
     * Static Instance
     *
     * @var static
     */
    private static $_instance = null;


    /**
     * PDO Object
     *
     * @var object
     */
    private $_pdo;

    /**
     * Query PDO
     *
     * @var stdClass
     */
    private $_query;

    /**
     * Count Fetches
     *
     * @var int
     */
    private $_count = 0;

    /**
     * Errors
     *
     * @var bool
     */
    private $_error = false;

    /**
     * Last Insert ID
     *
     * @var int
     */
    private $_lastInsertID;

    /**
     * DB constructor
     *
     * @return mixed
     */
    private function __construct()
    {
        try {

            $this->_pdo = new PDO('mysql:dbhost=localhost;dbname=test;','root','');

            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

            $this->_pdo->exec('SET NAMES utf8');

        }catch(PDOException $e) {

            echo $e->getMessage();
        }
    }

    /**
     * Get Instance
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(static::$_instance)) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * Generate Query Statement
     *
     * @param $sql string;
     * @param $bindings array;
     * @return $this;
     */
    public function query($sql, $bindings = [])
    {
        if ($this->_query = $this->_pdo->prepare($sql)) {

            $count  = 1;

            foreach ($bindings as $binding) {

                $this->_query->bindValue($count, $binding);

                ++$count;
            }

            if ($this->_query->execute()) {

                $this->_error = false;

                $this->_count = $this->_query->rowCount();

                $this->_lastInsertID = $this->_pdo->lastInsertId();

                return $this;

            }else {

                $this->_error = true;

            }
        }
    }

    public function action($action , $table, $wheres = [])
    {
        if (count($wheres) == 3) {

            $field = $wheres[0];
            $operator = $wheres[1];
            $value = $wheres[2];

        }

        $sql = "{$action} FROM `{$table}` WHERE {$field} {$operator} ?";

        if (!$this->query($sql, [$value])->_error) {

            return $this;

        }

        return false;
    }

    /**
     * GET Row
     *
     * @param $table string
     * @param $wheres array
     * @return $this
     */
    public function get($table , $wheres = [])
    {
        return $this->action('SELECT *', $table , $wheres);
    }

    /**
     * Get All Rows
     *
     * @param $table
     * @return bool|DB
     */
    public function getAll($table)
    {
        return $this->get($table, [true, '=', true]);
    }

    /**
     * Delete Row
     *
     * @param $table string
     * @param $wheres array
     * @return $this
     */
    public function delete($table , $wheres = [])
    {
        return $this->action('DELETE', $table, $wheres);
    }

    /**
     * Insert Row
     *
     * @param $table
     * @param array $data
     * @return DB
     */
    public function insert($table, $data = [])
    {
        $sql = "INSERT INTO `{$table}` SET";

        $fields = array_keys($data);

        foreach ($fields as $field) {
            $sql .= " `{$field}` = ?,";
        }

        $sql = rtrim($sql , ',');

        $sql .= ';';

        if (!$this->query($sql, $data)->_error) {
            return true;
        }

        return false;
    }

    /**
     * Update Row
     *
     * @param $table
     * @param array $data
     * @param array $wheres
     * @return bool
     */
    public function update($table, $data = [], $wheres = [])
    {
        $sql = "UPDATE `{$table}` SET";

        if (count($wheres) == 3) {

            $fieldCondition = $wheres[0];
            $operator = $wheres[1];
            $value = $wheres[2];
        }

        $fields = array_keys($data);

        foreach ($fields AS $field) {
            $sql .= " `{$field}` = ?,";
        }

        $sql = rtrim($sql, ',');

        $sql .= " WHERE `{$fieldCondition}` {$operator} ? ;";

        $bindings = array_merge($data , [$value]);

        if (!$this->query($sql, $bindings)->_error) {
            return true;
        }

        return false;
    }

    /**
     * Get Rows Results
     *
     * @return mixed
     */
    public function results()
    {
        return $this->_query->fetchAll();
    }

    /**
     * Get Row Result
     *
     * @return mixed
     */
    public function result()
    {
        return $this->_query->fetch();
    }

    /**
     * Get Last Insert ID
     *
     * @return int
     */
    public function lastInsertID()
    {
        return $this->_lastInsertID;
    }

    /**
     * Get Row Count
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->_count;
    }

}