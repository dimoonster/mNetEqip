<?php
cload("database.dbquery");
cload("database.dbrow");
/**
 * Description of user
 *
 * @author Di_Moon
 */
class user extends dbrow {
    protected $_guest;
    
    function __construct($dbh, $uid=0) {
        parent::__construct($dbh, "users", "id");
        $this->_guest = true;
        $this->set("id", $uid);
        
        // Если UID=0, то пытаемся инициализировать
        // из сессионных данных
        if($uid == 0) {
            // Прверим, нет ли в сессии id пользователя
            if(isset($_SESSION["uid"])) {
                $uid = (int)$_SESSION["uid"];
                $this->set("id", $uid);
            
                // Если пользователь с указанным id существует, то мы возможно не гость
                if($this->load()) {
                    // проверим, не отключен ли пользователь
                    if($this->get("enabled") == 1) {
                        $this->set("_guest", false);
                        $this->set("lastSeen", strftime("%F %T"));
                        $this->_save_no_audit();			// обновляем инфу о пользователе, без аудита
                    }
                }
            }
        } else {
            if($this->load()) $this->_guest = false;
        }
    }
    
    function login($username, $password) {
        $password = md5($password);
        $sql = new dbquery($this->_dbh);
        $sql->select()->from($this->_prfxtable)
                ->where("name='".addslashes($username)."' and password='$password'");
        $sth = $this->_dbh->query($sql);
        if($fa = $this->_dbh->raw_fetch_array($sth)) {
            $this->setElements($fa);
            $_SESSION["uid"] = $this->getUID();
            $this->set("ip", $_SERVER["REMOTE_ADDR"]);
            $this->save();
            return true;
        }
        
        return false;
    }
    
    public static function logout() {
        unset($_SESSION["uid"]);
    }
    
    // Возвращает ID пользователя
    function getUID() { return $this->get("id"); }
    
    // возвращает true если пользователь гость
    function guest() { return $this->_guest; }
    
    function disableUser() {
        $this->set("enabled", 0);
        $this->save();
    }
    
    function enableUser() {
        $this->set("enabled", 1);
        $this->save();
    }
}

?>
