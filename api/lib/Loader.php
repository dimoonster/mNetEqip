<?php
error_reporting(E_ALL & ~E_NOTICE);
//error_reporting(E_ALL);
date_default_timezone_set("Europe/Moscow");
//ini_set('session.save_path', dirname(__FILE__)."/../../tmp");
//if (!isset($_SESSION)) session_start();
//session_set_cookie_params(0, "/", ".kino4apple.ru");
@session_start();
//if(!isset($_REQUEST[session_name()])) {
//    session_start();
//    setcookie('PHPSESSID', session_id(), 0, '/', '.kino4apple.ru');
//}


if(!@constant("LOADER_DEBUG")) @define("LOADER_DEBUG", 0);

cload("object");
cload("database.db");
cfgload("dbconfig");
cload("user.user");
cload("audit");

abstract class Loader {
    protected static $imported = array();       // Импортированные классы
    protected static $database = null;
    protected static $config = array();         // импортированные конфиги
    protected static $user = null;              // Пользователь
    protected static $audit = null;		// класс аудита

    // Импорт объекта
    public static function import($class) {
        // Если класс был включён ранее, то процедуру импорта выполнять не будем
        if(!isset(self::$imported[$class])) {
            $result = false;
            $dirpath = dirname(__FILE__);

            $path = str_replace('.', DIRECTORY_SEPARATOR, $class);

            if(is_file($dirpath."/".$path.".php")) {
                if(LOADER_DEBUG) echo $dirpath."/".$path.".php loaded\n";
                $result = (bool)include_once($dirpath."/".$path.".php");
            } else {
                if(LOADER_DEBUG) echo $dirpath."/".$path.".php not found!\n";
            }
            self::$imported[$class]=$result;
        }
        return self::$imported[$class];
    }

    // Статистика: кол-во подключенных библиотек
    public static function stat_imported() {
        return sizeof(self::$imported);
    }
    public static function stat_config() {
        return sizeof(self::$config);
    }

    // Подключение к БД
    public static function dbconnect($config=null) {
        self::$database = new DB($config);
        self::$database->connect();
    }

    // Возвращает класс-подключение к БД
    public static function dbh() {
        if(self::$database == null) {
            self::dbconnect();
        }
        $r = &self::$database;
        return $r;
    }

    // импорт конфигов
    public static function config_import($cfg) {
        if(!isset(self::$config[$cfg])) {
            $result = false;
            if(is_file(dirname(__FILE__)."/../cfg/".$cfg.".php")) {
                if(LOADER_DEBUG) echo dirname(__FILE__)."/../cfg/".$cfg.".php\n";
                $result = (bool)include_once(dirname(__FILE__)."/../cfg/".$cfg.".php");
            }
            self::$config[$cfg]=$result;
        }
        return self::$config[$cfg];
    }

    // получение класса пользователя
    public static function user() {
        if(self::$user != null) return self::$user;

        self::$user = new user(self::dbh());
        return self::$user;
    }

    // Получение класса аудита
    public static function audit($dbh) {
        if(self::$audit != null) return self::$audit;

        self::$audit = new clsAUDIT($dbh);
        return self::$audit;
    }
}

function cload($class) {
    return Loader::import($class);
}

function cfgload($cfg) {
    return Loader::config_import($cfg);
}

function dbh() {
    return Loader::dbh();
}

function _sessionUser() {
    return Loader::user();
}

function _print_r($var) {
    echo "<pre>";
    print_r($var);
    echo "</pre>";
}

//function _AUDIT($dbh) {
//    return Loader::audit($dbh);
//}
?>
