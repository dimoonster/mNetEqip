<?php
// Параметры доступа к БД
$config = new stdClass();
$config->host = "172.16.1.64";
$config->user = "mgmt";
$config->pass = "mgmt123";
$config->name = "mooncontrol";
$config->type = "pgsql";
$config->tbl_prefix = "";

// -------------------------------------------------------------------------
abstract class DBConfig {
    protected static $cfg;
    protected static $query_count = 0;

    public static function init($config) {
        self::$cfg = $config;
    }

    protected static function element($elem, $value=null) {
        if($value==null) {
            if(isset(self::$cfg->$elem)) return self::$cfg->$elem;
        }

        self::$cfg->$elem = $value;
        return $value;
    }

    public static function user($user=null) {
        return self::element("user", $user);
    }

    public static function host($host=null) {
        return self::element("host", $host);
    }

    public static function pass($pass=null) {
        return self::element("pass", $pass);
    }

    public static function name($name=null) {
        return self::element("name", $name);
    }

    public static function type($type=null) {
        return self::element("type", $type);
    }

    public static function tbl_prefix($tbl_prefix=null) {
        return self::element("tbl_prefix", $tbl_prefix);
    }

    public static function toString() {
        $rv = "DBConfig: type=".self::type()."; host=".self::host()."; user=".self::user()."; name=".self::name();
        return $rv;
    }

    // счётчик запросов
    public static function qc() {
        self::$query_count++;
    }

    public static function get_qc() {
        return self::$query_count;
    }
}
DBConfig::init($config);
?>