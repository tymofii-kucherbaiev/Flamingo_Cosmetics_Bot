<?php
require $_SERVER['DOCUMENT_ROOT'] . '/config/function.php';
$directory = 'smakolyky.org/connect.php';

$DB_database = 'mn469049_db';
$DB_hostname = 'mn469049.mysql.tools';
$DB_username = 'mn469049_db';
$DB_password = 'jPWQQ8U9';
$DB_keygen = 'm205r1G6NHNs'; //12 values
$DB_botname = 'Lamour_Famille_Bot'; //12 values

$SQL = new SQL ($DB_database, $DB_hostname, $DB_username, $DB_password, $DB_keygen, $DB_botname);
$API = new API('5663303135:AAEr_S-ue-tivrF6WRfpD94_KnR9BAOAjxs');


if (!$SQL->SELECT_FROM('*', 'config', "`Value` LIKE 'WebHook'")->num_rows) {
    $setWebhook = json_decode($API->setWebhook(directory: $directory), true);

    $SQL->INSERT_INTO('config',
    '(Value, Description, Active)',
    "'" . explode(' ', $setWebhook['description'])[0] . "', '$directory', '{$setWebhook['result']}'");
}

$SQL->connect_close();

file_put_contents('json.json', $data = file_get_contents('php://input'));

