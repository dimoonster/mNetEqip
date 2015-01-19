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
        $this->querysql("SET NAMES UTF-8;");
        $this->querysql("SET CHARACTER SET UTF-8;");

        return $this->dbh;
    }

    public function querysql($sql) {
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

}
?>
