<?php

define("DB_DRIVER_DEBUG", 0);

cload("object");
cload("database.dbrow");

abstract class DBAbstract {
    protected $dbh;
    protected $sth;
    protected $q;
    protected $cnt;	// Счётчик запросов
    protected $sqlLog;	// Log sql-запросов

    public function __construct() {
	$this->sqlLog = array();
        if(!$this->connect()) {
            throw new Exception("DB Subsystem: error to connect to db");
        }
    }

    abstract public function connect();         // Подключение к БД
    abstract public function querysql($sql, $skipaudit=0);    // Выполнение запроса
    abstract public function raw_fetch_array($sth=null);        // Возвращает строку данных в виде обьекта
    abstract public function quote_tbl();
    abstract public function quote_fld();
    abstract public function get_last_id();
    abstract public function getColumns($tblName);		// возвращает список полей таблицы

    public function query($q) {
        $sth = $this->querysql($q->__toString());
        $this->q = $q;

        return $sth;
    }

    public function fetch_array($sth=null) {
        return $this->raw_fetch_array($sth);
    }
    
    public function getCountQueries() {
	return $this->cnt;
    }
    
    public function getSQLLog() {
	return $this->sqlLog;
    }

}
?>
