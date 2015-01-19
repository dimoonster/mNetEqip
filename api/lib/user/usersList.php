<?php
cload("database.dbquery");
cload("object");
/**
 * Description of usersList
 *
 * @author Di_Moon
 */
class usersList {
    protected $_dbh;
    protected $_users;
    protected $_usersArr;
    
    function __construct($dbh, $order="id", $ordertype="asc") {
        $this->_dbh = $dbh;
        $this->_users = array();
        $this->_usersArr = array();
        
        $sql = new dbquery($this->_dbh);
        $sql->select()->from("users")->order("$order $ordertype");
        
        $sth = $this->_dbh->query($sql);
        while($fa=$this->_dbh->raw_fetch_array($sth)) {
            $_obj = new object($fa);
            $this->_users[$_obj->get("name")] = $_obj;
            $this->_usersArr[$_obj->get("name")] = $_obj->getElements();
        }
    }
    
    function getList() { return $this->_users; }
    
    function getListArray() { return $this->_usersArr; }
}

?>
