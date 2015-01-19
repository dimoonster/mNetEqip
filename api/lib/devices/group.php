<?php
namespace devices;

cload("object");
cload("database.dbrow");
cload("database.dbquery");
cload("device");

use \object, \dbrow, \dbquery;

class group extends dbrow {

    private $_devices;          // Устройства группы
    private $_subGroups;        // Подгруппы группы

    // ----------------------------------------------------
    //    $dbh - связь с БД
    //    $id - id группы
    //    $grpData - данные существующе группы (приходят при рекурсии)
    function __construct($dbh, $id=null, $grpData=null) {
        parent::__construct($dbh, "devices_groups", "id", $grpData);

        $this->_devices = null;
        $this->_subGroups = null;

        // Если не был указан id группы или есть данные группы, то завершим инициализацию
        if(!$id || $grpData) return;

        if($this->load($id)) {
            // Группа есть в базе и информация о ней загружена
            $this->_devices = $this->loadDevices();
            $this->_subGroups = $this->loadSubGroups();
        }
    }

    // Получение из базы список устройств входящих в группу
    protected  function loadDevices() {
        $_devs = array();
        $sql = (new dbquery($this->_dbh))->select()->from("devices")->where("group_id=".$this->get("id"));
        $_sth = $this->_dbh->query($sql);
        while($fa=$this->_dbh->raw_fetch_array($_sth)) {
            $_devObj = new object($fa);
            $_devs[$_devObj->get("name")] = new device($this->_dbh, $_devObj);
        }

        return $_devs;
    }

    // Отдадим список устройств
    public function getDevices() {
        // Если данные о устройствах не были загружены, то загрузим их
        if(!$this->_devices) $this->_devices = $this->loadDevices();

        return $this->_devices;
    }

    // Получим список подгрупп группы
    protected  function loadSubGroups() {
        $_groups = array();

        $sql = (new dbquery($this->_dbh))->select()->from("devices_groups")->where("parentgroup=".$this->get("id"));
        $sth = $sql->execute();
        while($fa=$this->_dbh->raw_fetch_array()) {
            $_grpObj = new object($fa);
            $_groups[$fa->get("name")] = new group($this->_dbh, $_grpObj->get("id"), $_grpObj);
        }

        return $_groups;
    }

    // Отдадим подгруппы
    public function getSubGroups() {
        if(!$this->_subGroups) $this->_subGroups = $this->loadSubGroups();

        return $this->_subGroups;
    }

    public function addGroup(object $grpData) {

    }
} 