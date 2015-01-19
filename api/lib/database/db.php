<?php

cfgload("dbconfig");
cload("database.drivers.".DBConfig::type());

class DB extends DBDriver {
    public function __construct($config=null) {
        parent::__construct();
    }


}
?>
