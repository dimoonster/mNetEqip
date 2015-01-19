<?php

define("DBQUERY_TYPE_SELECT", 1);
define("DBQUERY_TYPE_UPDATE", 2);
define("DBQUERY_TYPE_INSERT", 3);
define("DBQUERY_TYPE_DELETE", 4);

class dbquery {
    private     $dbh;                   // Класс связи с БД

    protected   $q_type;                // Тип запроса
    protected   $q_table;               // Таблица
    protected   $q_fields;              // Поля запроса

    protected   $q_where;               // Поле where

    private     $quote_tbl;
    private     $quote_fld;
    
    private     $q_order;               // Сортировка
    private     $q_limit_from;          // Выборка ограниченного набра данных. Поле С
    private     $q_limit_row;           // Выборка ограниченного набра данных. Количество выбираемых записей
    private     $q_top;                 // Выборка первых n значений

    public function __construct($dbh, $prefix="") {
        $this->dbh = $dbh;
        $this->q_fields = array();
        $this->q_where = "";

        $this->quote_tbl = $this->dbh->quote_tbl();
        $this->quote_fld = $this->dbh->quote_fld();
        
        $this->q_limit_from = "";
        $this->q_limit_row = "";
    }

    public function select() { $this->q_type = DBQUERY_TYPE_SELECT; return $this; }
    public function update($tbl) { $this->q_type = DBQUERY_TYPE_UPDATE; $this->set_table($tbl); return $this; }
    public function insert() { $this->q_type = DBQUERY_TYPE_INSERT; return $this; }
    public function delete($tbl) { $this->q_type = DBQUERY_TYPE_DELETE; $this->set_table($tbl); return $this; }

    public function set_table($tbl) { $this->q_table = $tbl; }
    public function from($tbl) { $this->set_table($tbl); return $this; }
    public function into($tbl) { $this->set_table($tbl); return $this; }
    public function order($clause) { $this->q_order = $clause; return $this; }
    public function limit($from, $rows) { $this->q_limit_from = $from; $this->q_limit_row = $rows; return $this; }
    public function top($n) { $this->q_top = $n; return $this; }

    public function field($field, $value=null) {
        $this->q_fields[$field] = $value;
        return $this;
    }

    public function where($clause) {
	if($clause===null) return $this;
        $this->where = "where ".$clause;
        return $this;
    }

    public function __toString() {
        switch($this->q_type) {
            case DBQUERY_TYPE_SELECT: {
                return $this->generate_select();
            }; break;
            case DBQUERY_TYPE_UPDATE: {
                return $this->generate_update();
            }; break;
            case DBQUERY_TYPE_INSERT: {
                return $this->generate_insert();
            }; break;
            case DBQUERY_TYPE_DELETE: {
                return $this->generate_delete();
            };
            default: {
                throw new Exception("dbquery.__toString: QUERY_TYPE is UNKNOWN");
            };
        }
    }

    public function generate_select() {
        $sql = "";
        $top = "";
        
        $pattern = "select %s %s from ".$this->quote_tbl."%s".$this->quote_tbl;
        $fields = "";

        if(sizeof($this->q_fields)>0) {
            foreach($this->q_fields as $k=>$v) {
                $fields .= $k.", ";
            }
            $fields = substr($fields, 0, -2);
        } else {
            if($fields=="") $fields = "*";
        }
        
        if($this->q_top != "") $top = "top ".$q_top;

        $sql = sprintf($pattern, $top, $fields, $this->q_table);

        if($this->where!="") $sql .= " ".$this->where;
        
        if($this->q_order!="") $sql .= " order by ".$this->q_order;
        
        if(is_numeric($this->q_limit_from) && $this->q_limit_row>0) 
            $sql .= " limit ".$this->q_limit_from.", ".$this->q_limit_row;

        return $sql.";";
    }

    public function generate_update() {
        $sql = "";
        $pattern = "update ".$this->quote_tbl."%s".$this->quote_tbl." set %s";
        $fields = "";

        if(sizeof($this->q_fields)>0) {
            foreach($this->q_fields as $k=>$v) {
                $fields .= $k."=".(is_int($v)?$v:"'$v'").", ";
            }
            $fields = substr($fields, 0, -2);

            $sql = sprintf($pattern, $this->q_table, $fields);
            if($this->where!="") $sql .= " ".$this->where;
            return $sql.";";
        }

        return false;
    }

    public function generate_insert() {
        $sql = "";
        $pattern = "insert into ".$this->quote_tbl."%s".$this->quote_tbl."(%s) values(%s);";
        $fileds = "";
        $values = "";

        if(sizeof($this->q_fields)>0) {
            foreach($this->q_fields as $k=>$v) {
                $fields .= $k.", ";
                $values .= (is_int($v)?$v:"'$v'").", ";
            }
            $fields = substr($fields, 0, -2);
            $values = substr($values, 0, -2);

            $sql = sprintf($pattern, $this->q_table, $fields, $values);
            return $sql;
        }

        return false;
    }

    public function generate_delete() {
        $sql = "";
        $pattern = "delete from ".$this->quote_tbl."%s".$this->quote_tbl;

        $sql = sprintf($pattern, $this->q_table);
        if($this->where!="") $sql .= " ".$this->where;

        return $sql.";";
    }
    
    // Выполнение запроса
    public function execute() {
	return $this->dbh->query($this);
    }
    
    // Возврат результата выполнения запроса (результат - массив объектов)
    public function fetch_object() {
	$sth = $this->execute();
	$rv = array();
	while($fa=$this->dbh->raw_fetch_array($sth)) {
	    array_push($rv, new object($fa));
	}
	return $rv;
    }
    
    // Возврат результата выполнения запроса (результат - массив массивов)
    public function fetch_array() {
	$rv = array();
	$_data = $this->fetch_object();
	foreach($_data as $row) {
	    array_push($rv, $row->getElements());
	}
	return $rv;
    }
}

#$q = new dbquery(null);

#$q->select()->field("field1")->field("field2")->from("Table1");

#echo $q->__toString()."\n";
?>
