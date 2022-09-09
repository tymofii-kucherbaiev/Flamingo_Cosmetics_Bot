<?php
require 'function.php';

$DB_database = 'mn469049_db';
$DB_hostname = 'mn469049.mysql.tools';
$DB_username = 'mn469049_db';
$DB_password = 'jPWQQ8U9';
$DB_keygen = 'm205r1G6NHNs'; //12 values


//$sql = new SQL ($DB_database, $DB_hostname, $DB_username, $DB_password, $DB_keygen);

//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
//$mysqli = new mysqli(hostname: $DB_hostname, username: $DB_username, password: $DB_password, database: $DB_database);
//$sql->connect_open()->query("CREATE TABLE test (`id` INT)");
//$sql->link();
//$sql->CREATE_TABLE('text', "`id` INT");


$api = new API('5663303135:AAEr_S-ue-tivrF6WRfpD94_KnR9BAOAjxs');
var_dump($api->setWebhook('smakolyky.org/config/corn.php'));

$data = file_get_contents('php://input');

file_put_contents('json.json', $data);
//echo '<xmp>';
//var_dump($sql);
//echo '</xmp>';
//$link->query("CREATE TABLE test (`id` INT)");
//echo $mysqli;
