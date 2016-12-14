<?php
$DB_NAME='vitlag_igrushki';
$DB_LOGIN='vitlag_admin';
$DB_PASSWORD='vitexchange';
//==================================================================================================================
function sql($sql){
global $SQLERR;
$SQLERR=0;
$result=mysql_query($sql);
$rows = mysql_affected_rows(); 
 if($rows != -1) {
 	return $result;
 }	
 else {
  echo "<hr><font color=#EE0000><b>Error:</b></font> ".mysql_error()."<br>$sql<hr>"; 
  $SQLERR=1;
  return 0;
 }
}
//==================================================================================================================
function connect(){
global $link,$DB_PASSWORD,$DB_LOGIN,$DB_NAME;
$link=@mysql_connect("localhost",$DB_LOGIN,$DB_PASSWORD);
 if (!$link){
  echo "database connection error!";
  exit;
 }  
mysql_select_db($DB_NAME);
sql ("SET NAMES 'cp1251';");
}
//==================================================================================================================
function disconnect(){
global $link;
mysql_close($link);
$link = 0;
}
?>