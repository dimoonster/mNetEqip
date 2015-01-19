<?php
// Класс строка данных из БД

cfgload("dbconfig");
cload("object");

class dbrow_debug extends object {
    protected   $_table;                // Таблица
    protected   $_idx;                  // Поле уникального индека

    protected   $_prfxtable;            // имя таблицы с учётом префикса

    protected   $_dbh;                  // Связь с бд

    // Конструктор класса
    //          $dbh - класс-связь с БД
    //          $table - таблица, откуда получена строка с данными. если null - то данные с сложного запроса, нельзя сохранять изменения
    //          $idx - поле уникального индека, относительно которого будут происходить действия с данными
    //          $data - данные строки
    public function __construct($dbh, $table=null, $idx=null, $data=null) {
        $this->_dbh     = $dbh;

        $this->index($idx);
        parent::__construct($data);
        $this->table($table);
    }

    // Установка таблицы данных
    //         $table - установка таблицы для данных. если = null, то просто возратит имя текущей установленной таблицы
    public function table($table=null) {
        if($table==null) return $this->_table;

        $this->_table = $table;
        $this->_prfxtable =
                $this->_dbh->quote_tbl().
                (($table==null)?null:(DBConfig::tbl_prefix()!=""?DBConfig::tbl_prefix()."_".$table:$table)).
                $this->_dbh->quote_tbl();

        return $this->_table;
    }

    // Установка индекса записи
    //    в случае входа null, возвращает имя индексного поля
    public function index($field=null, $value=null) {
        if($field==null) return $this->_idx;

        $this->_idx = $field;
        $this->set($field, $value);

        return $this->_idx;
    }

    // Загрузка данных из таблицы
    public function load($index=null) {
	//echo "i load";
        if( $this->_prfxtable && ($this->get($this->_idx, $index)) ) {
            $sql = "select * from ".$this->_prfxtable." where ".$this->_idx."='".addslashes($this->get($this->_idx, $index))."'";
//            echo $sql;
            $this->_dbh->querysql($sql);
            if($fa = $this->_dbh->raw_fetch_array()) {
                $this->setElements($fa);
                return true;
            }
//            return true;
        }

        return false;
    }
    
    // Генерация запроса insert
    protected function _insert($index=null) {
        $sql = "insert into ".$this->_prfxtable."(%s) values(%s)";
        $fld_list = "";
        $val_list = "";
        $elems = $this->getElements();
        foreach($elems as $field=>$value) {
            if($field!=$this->_idx) {
                if($value!=null) {
                    $fld_list .= $this->_dbh->quote_fld()."$field".$this->_dbh->quote_fld().", ";
                    $val_list .= is_int($value)?"$value, ":"'".addslashes($value)."', ";
                }
            }
        }
        return sprintf($sql, substr($fld_list, 0, -2), substr($val_list, 0, -2));
    }
    
    // Генерация запроса insert c использованием всех полей данных
    protected function _insertWithIndex($index=null) {
        $sql = "insert into ".$this->_prfxtable."(%s) values(%s)";
        $fld_list = "";
        $val_list = "";
        $elems = $this->getElements();
        foreach($elems as $field=>$value) {
            if($value!=null) {
                $fld_list .= $this->_dbh->quote_fld()."$field".$this->_dbh->quote_fld().", ";
                $val_list .= is_int($value)?"$value, ":"'".addslashes($value)."', ";
            }
        }
        return sprintf($sql, substr($fld_list, 0, -2), substr($val_list, 0, -2));
    }
    
    // Генерация запроса update
    protected function _update($index=null) {
        $sql = "update ".$this->_prfxtable." set %s where %s;";
        $elems = $this->getElements();
        $set_list = "";
        foreach($elems as $field=>$value) {
            if($field!=$this->_idx) {
                $set_list .= $this->_dbh->quote_fld()."$field".$this->_dbh->quote_fld()."=".($value==null?'NULL':(is_int($value)?"$value":"'".addslashes($value)."'")).", ";
            }
        }
        $idx = addslashes($this->get($this->_idx, $index));
        $where = $this->_idx."=".(is_int($idx)?$idx:"'$idx'");
//        $this->_dbh->querysql(sprintf($sql, substr($set_list, 0, -2), $where));
        return sprintf($sql, substr($set_list, 0, -2), $where);
    }

    // Сохранение данных в таблице
    public function save($index=null) {
        if( $this->_prfxtable ) {

            // Если есть установленное значение индекса, то делаем update
            if($this->get($this->_idx, $index)) {
                $sql = $this->_update($index);
            } else {
            // Если зеначение индекса не установлено, делаем insert
                $sql = $this->_insert($index);
            }
//            echo $sql;
            $this->_dbh->querysql($sql);

            return true;
        }

        return false;
    }

    // Сохранение данных в таблице без аудита этого действия
    public function _save_no_audit($index=null) {
        if( $this->_prfxtable ) {

            // Если есть установленное значение индекса, то делаем update
            if($this->get($this->_idx, $index)) {
                $sql = $this->_update($index);
            } else {
            // Если зеначение индекса не установлено, делаем insert
                $sql = $this->_insert($index);
            }
//            echo $sql;
            $this->_dbh->querysql($sql, 1);

            return true;
        }

        return false;
    }
    
    // Возвращает список полей таблицы
    public function getColumns() {
	return $this->_dbh->getColumns($this->_prfxtable);
    }
}
?>
