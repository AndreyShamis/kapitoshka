<?php
/**
 * Created by PhpStorm.
 * User: Andrey Shamis
 * Date: 6/29/15
 * Time: 12:24 PM
 */


class DataBaseBaseClass{
    //  Here provide your information for MySQL connection
    protected $db_host          = "127.0.0.1";         //  host
    protected $db_user          = "";               //  User to database
    protected $db_pass          = "";               //  Password
    protected $db_db            = "";               //  DATA base name
    protected $m_LastSql        = "";
    protected $link;
    protected $m_LastQuery;
    protected $m_DebugErrors    = 1;


    public function disableDebug(){
        $this->m_DebugErrors = 0;
    }
}

class DataBasePDO extends DataBaseBaseClass{

    public function __construct(){
        try {
            # MS SQL Server и Sybase через PDO_DBLIB
            //$this->link = new PDO("mssql:host=$this->db_host;dbname=$this->db_db", $this->db_user, $this->db_pass);
            //$this->link = new PDO("sybase:host=$this->db_host;dbname=$this->db_db", $this->db_user, $this->db_pass);

            # MySQL через PDO_MYSQL
            $this->link = new PDO("mysql:host=$this->db_host;dbname=$this->db_db", $this->db_user, $this->db_pass);

            # SQLite
            //$this->link = new PDO("sqlite:my/database/path/$this->db_db");
        }
        catch(PDOException $e) {
            //echo $e->getMessage();
        }
    }

}
/**
 * Class DataBase
 */
class DataBase extends DataBaseBaseClass{
    /**
     * Escape a string before sql request
     * @param $string -  input
     * @return string - output
     */
    public function real_escape_string($string){
        $ret = "";
        try{
            $ret = mysqli_real_escape_string($this->link,$string);
        }
        catch(Exception $ex){

        }
        return $ret;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->link = new mysqli($this->db_host, $this->db_user, $this->db_pass,$this->db_db);
        if(mysqli_connect_errno()){
            echo "CONSTRUCT ERROR:" . mysqli_error($this->link);
        }
        if (!$this->link){
            die('Could not connect: ' .
                mysqli_error($this->link) .
                "<br/>" .
                mysqli_errno($this->link) .
                "<br/><br/>");
        }

        mysqli_select_db($this->link,$this->db_db);
        if(mysqli_errno($this->link)){
            echo "CONSTRUCT ERROR:" . mysqli_error($this->link);
        }
    }

    /**
     * Destructor
     */
    public function __destruct(){
        mysqli_close($this->link);
    }

    /**
     * Performs a query on the database
     * @param The query string. $sql
     * @return Returns FALSE on failure. For successful SELECT, SHOW,
     * DESCRIBE or EXPLAIN queries mysqli_query() will return a mysqli_result
     * object. For other successful queries mysqli_query() will return TRUE.
     */
    public function query($sql){
        $this->m_LastSql = $sql;
        $ret =  mysqli_query($this->link, $this->m_LastSql);
        $this->m_LastQuery  = $ret;
        if($this->m_DebugErrors>0)
            $this->checkMysqlErrors();

        return $ret;
    }

    /**
     * Gets the number of rows in a result
     * @param Procedural style only $result
     * @return Returns number of rows in the result set.
     */
    public function count($result){
        if($result == null)
            $result = $this->m_LastQuery;
        if($result == null)
            return 0;
        return mysqli_num_rows($result);
    }

    /**
     *  Fetch a result row as an associative, a numeric array, or both
     * @param Procedural style only $result
     * @return Returns an array of strings that corresponds to the fetched row or NULL if there are no more rows in resultset.
     */
    public function fetch_array($result){
        return mysqli_fetch_array($result);
    }

    /**
     * Fetch a result row as an associative array
     * @param Procedural style only $result
     * @return Returns an associative array of strings representing the
     * fetched row in the result set, where each key in the array represents
     * the name of one of the result set's columns or NULL if there are
     * no more rows in resultset.
     */
    public function fetch_assoc($result){
        return mysqli_fetch_assoc($result);
    }

    /**
     * Returns a string description of the last error
     * @return A string that describes the error. An empty string if no error occurred.
     */
    public function error(){
        return mysqli_error($this->link);
    }

    /**
     * Returns the error code for the most recent function call
     * @return An error code value for the last call, if it failed. zero means no error occurred.
     */
    public function errno(){
        return mysqli_errno($this->link);
    }

    /**
     * Returns the auto generated id used in the last query
     * @return The value of the AUTO_INCREMENT field that was updated by the
     * previous query. Returns zero if there was no previous query on the
     * connection or if the query did not update an AUTO_INCREMENT value.
     */
    public function insert_id(){
        return mysqli_insert_id($this->link);
    }

    public function checkMysqlErrors(){
        if($this->errno()>0 && $this->errno() != 1062){
            echo "SQL ERROR:[". $this->errno() . "]" . $this->error() . "<br/>";
            echo $this->m_LastSql . "<br/>";
        }
        return $this->errno();
    }
}

$db = new DataBase();