<?php
cload("database.drivers.dbabstract");

class DBDriver extends DBAbstract {

    public function connect() {
        if(DBConfig::type() != "mysql") throw new Exception("Mysql driver: wrong DBConfig db type");

        $this->dbh = mysqli_connect(
                            DBConfig::host(),
                            DBConfig::user(),
                            DBConfig::pass(),
                            DBConfig::name()
                    );
        if(!$this->dbh) echo "err";
#        $this->querysql("set character_set_results='utf8'");
        $this->querysql("set character_set_results='utf8'", 1);
        $this->querysql("set collation_connection='utf8_general_ci'", 1);

        return $this->dbh;
    }

    public function querysql($sql, $skipaudit=0) {
	// Аудит запросов к БД
	if($skipaudit==0) AUDIT::auditsql($sql);
	if(!preg_match("'audit'si", $sql)) array_push($this->sqlLog, $sql);
	
//	echo $sql;
	$this->cnt++;
        $this->sth = mysqli_query($this->dbh, $sql);
        return $this->sth;
    }

    public function raw_fetch_array($sth=null) {
        return mysqli_fetch_object($sth==null?$this->sth:$sth);
    }

    public function quote_tbl() {
        return "";
    }

    public function quote_fld() {
        return "`";
    }
    
    public function get_last_id() {
	return mysqli_insert_id($this->dbh);
    }
    
    public function getColumns($tblName) {
	$sql = "show columns from ".$this->quote_tbl().$tblName.$this->quote_tbl.";";
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
