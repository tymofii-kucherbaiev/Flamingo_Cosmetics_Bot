<?php
if (file_get_contents('php://input')) {
    require './config/function.php';

    $sql_hostname = 'mn469049.mysql.tools';
    $sql_database = 'mn469049_db';
    $sql_username = 'mn469049_db';
    $sql_password = 'jPWQQ8U9';

    $mysqli = new PDO("mysql:host=$sql_hostname;dbname=$sql_database", $sql_username, $sql_password);

    $core = new API('5484985114:AAEhGnuPiLBzTGlxYX8wrIdYgoxlxGDXKg0');

    $text = json_decode(file_get_contents('./config/text.json'), true)['content'];

    $text_keyboard = $text['keyboard'];
    $text_message = $text['message'];

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

    $core->user_id($data['from']['id']);

    $sql_result_user = $mysqli->query("CALL select_user($user_id)")->fetch();

    if (!$sql_result_user)
        $sql_users = $mysqli->query("CALL add_new_user($user_id, '$user_username', '$user_first_name', '$user_last_name')");

    if (array_key_exists('contact', $data)) {
        if ($data['reply_to_message']['text'] == $text_message['welcome']['no_authorize']) {
            $core->deleteMessage($user_id, $data['message_id']);
            $mysqli->query("CALL PC_update_user('phone_number', '{$data['contact']['phone_number']}', '$user_id')");

            $sql_result_user = $mysqli->query("CALL select_user($user_id)")->fetch();

            $keyboard = new Keyboard('keyboard', false);
            $keyboard = $keyboard->auto_create('main_menu', $text_keyboard, $sql_result_user, NULL, NULL);

            $callback_sendMessage = json_decode(
                $core->sendMessage(
                    $user_first_name . $text_message['welcome']['authorize'],
                    $keyboard,
                    NULL
                ), true);


            $mysqli->query("CALL PC_update_user('message_id', '{$callback_sendMessage['result']['message_id']}', '$user_id')");
            $core->deleteMessage($sql_result_user['message_id']);
        }
    } elseif (array_key_exists('callback_query', $input))
        require './query/callback.php';
    else
        require './query/private.php';

    $core->answerCallbackQuery(NULL, NULL, $data['id']);
    file_put_contents('json.json', file_get_contents('php://input'));
}
