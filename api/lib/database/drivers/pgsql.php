<?php
cload("database.drivers.dbabstract");

class DBDriver extends DBAbstract {

    public function connect() {
        if(DBConfig::type() != "pgsql") throw new Exception("pgsql driver: wrong DBConfig db type");

        $connectString = sprintf("host=%s port=5432 dbname=%s user=%s password=%s",
            DBConfig::host(),
            DBConfig::name(),
            DBConfig::user(),
            DBConfig::pass());

        $this->dbh = pg_connect($connectString);

        if(!$this->dbh) echo "err";

        pg_set_client_encoding($this->dbh, "UNICODE");

        #$this->querysql("set character_set_results='utf8'");
        #$this->querysql("set character_set_results='utf8'", 1);
        #$this->querysql("set collation_connection='utf8_general_ci'", 1);

        return $this->dbh;
    }

    public function querysql($sql, $skipaudit=1) {
        // Аудит запросов к БД
        if($skipaudit==0) AUDIT::auditsql($sql);
        if(!preg_match("'audit'si", $sql)) array_push($this->sqlLog, $sql);

//	echo $sql;
        $this->cnt++;
        $this->sth = pg_query($this->dbh, $sql);
        return $this->sth;
    }

    public function raw_fetch_array($sth=null) {
        return pg_fetch_object($sth==null?$this->sth:$sth);
    }

    public function quote_tbl() {
        return "'";
    }

    public function quote_fld() {
        return "`";
    }

    public function get_last_id() {
        return null;
    }

    public function getColumns($tblName) {
        $sql = "select column_name from INFORMATION_SCHEMA.COLUMNS where table_name = ".$this->quote_tbl().$tblName.$this->quote_tbl.";";
        $sth = $this->querysql($sql);
        $columns = array();
        while($fa=$this->raw_fetch_array()) {
            $column = (new object($fa))->getElements();
            array_push($columns, $column);
        }

        return $columns;
    }

}
?>