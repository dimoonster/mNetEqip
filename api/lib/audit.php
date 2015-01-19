<?php

cload("database.dbrow");

abstract class AUDIT {
    
    public static function auditsql($query) {
	$backtrace = debug_backtrace();
	
	if(preg_match("'audit_sql'si", $query)) return;				// сами себя не аудитим :)
	
	$query_type = "unknown";
	$pattern = "";
	if(preg_match("'^set'si", $query)) return;
	if(preg_match("'^select'si", $query)) return;				// select логировать не будем
	if(preg_match("'^update users'si", $query)) return;			
	if(preg_match("'^insert'si", $query)) { $query_type="insert"; $pattern="'insert into (.*?)[\s\(]'si"; }
	if(preg_match("'^update'si", $query)) { $query_type="update"; $pattern="'update (.*?)[\s+]'si"; }
	if(preg_match("'^delete'si", $query)) { $query_type="delete"; $pattern="'delete (.*?)[\s+]'"; }
	
	$query_table = "unknown";
	
	if($pattern!="") {
	    $match = array();
	    preg_match($pattern, $query, $match);
	    $query_table = $match[1];
	}
	
	$data = new object();
	$data->set("user", _sessionUser()->getUID());
	$data->set("date",date("Y-m-d H-i-s"));
	$data->set("query", $query);
	$data->set("query_type", $query_type);
	$data->set("query_table", $query_table);
	
	$backtrace = print_r($backtrace[2], true);
	
	$data->set("backtrace", $backtrace);
	
//	_print_r($data);
	$dbrow = new dbrow(dbh(), "audit_sql", "id", $data);
	$dbrow->save();
    }
}

?>