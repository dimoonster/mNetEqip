<?php
cload("object");
/**
 * Класс для работы с запросами _GET и _POST
 *
 * @author Di_Moon
 */
abstract class requestProcessor {
    
    // Функция по получению данных из HTTP запроса
    // приоритетен _POST данные
    public static function getVar($var, $default=null) {
        if(isset($_POST[$var])) return $_POST[$var];
        if(isset($_GET[$var])) return $_GET[$var];
        
        return $default;
    }
    
    // Возвращает _POST в виде объёкта
    public static function post() {
        $obj = new object($_POST);
        $obj->del("frame");
        return $obj;
    }
    
    // Возвращает _GET в виде объекта
    public static function get() {
        return new object($_GET);
    }
}

?>
