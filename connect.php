<?php
if (file_get_contents('php://input')) {
    require './config/function.php';

    $database_option = [
        "hostname" => 'mn469049.mysql.tools',
        "database" => 'mn469049_db',
        "username" => 'mn469049_db',
        "password" => 'jPWQQ8U9'
    ];
    $mysqli_option = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    );
    $mysqli = new PDO("mysql:host={$database_option['hostname']};dbname={$database_option['database']}",
        $database_option['username'], $database_option['password'], $mysqli_option);

    $core = new API('5484985114:AAEhGnuPiLBzTGlxYX8wrIdYgoxlxGDXKg0');

    $text_filling = json_decode(file_get_contents('./config/text.json'), true)['content'];

    $input = json_decode(file_get_contents('php://input'), true);
    if (array_key_exists('callback_query', $input)) {
        $data = $input['callback_query'];
        $action = explode(':', explode('|', $data['data'])[0])[1];
        $type = explode(':', explode('|', $data['data'])[1])[1];
    } else {
        $data = $input['message'];
    }

    $user_message = $data['text'];
    $user_id = $data['from']['id'];
    $user_first_name = $data['from']['first_name'];
    $user_last_name = $data['from']['last_name'];
    $user_username = addslashes($data['from']['username']);
    $message_id = $data['message_id'];

    $core->chat_id($data['from']['id']);

    $sql_result = $mysqli->query("CALL select_user($user_id)")->fetch();

    if (!$sql_result)
        $sql_users = $mysqli->query("CALL add_new_user($user_id, '$user_username', '$user_first_name', '$user_last_name')");

    if (array_key_exists('contact', $data)) {
        if ($data['reply_to_message']['text'] == $text_filling['new_user']) {
            $core->deleteMessage($data['message_id']);
            $mysqli->query("CALL PC_update_user('phone_number', '{$data['contact']['phone_number']}', '$user_id')");

            $sql_result = $mysqli->query("CALL select_user($user_id)")->fetch();

            $keyboard = new Keyboard('keyboard', false);
            $keyboard = $keyboard->create('main_menu', $text_filling, $sql_result, NULL, NULL);

            $callback = json_decode($core->sendMessage($user_first_name . $text_filling['complete'],
                    $keyboard,NULL), true);

            $mysqli->query("CALL PC_update_user('message_id', '{$callback['result']['message_id']}', '$user_id')");
            $core->deleteMessage($sql_result['message_id']);
        }
    } elseif (array_key_exists('callback_query', $input))
        require './query/callback.php';
    else
        require './query/private.php';

    $core->answerCallbackQuery(NULL, NULL, $data['id']);

}
