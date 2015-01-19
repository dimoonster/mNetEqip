<?php
namespace devices;

cload("object");
cload("database.dbrow");


use \dbrow, \dbquery, \object;

class device extends dbrow {
    var $_connectionPoints;         // Точки подключения оборудования

    // ---------------------------------------------
    //      $dbh - соединение с БД
    //      $deviceArr - данные информации о устройстве
    function __construct($dbh, $deviceArr=null) {
        parent::__construct($dbh, "device", "id", $deviceArr);
    }
}

?>