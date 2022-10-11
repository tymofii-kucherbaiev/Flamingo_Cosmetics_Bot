<?php
if (file_get_contents('php://input')) {
    require './config/function.php';






    $DB_database = 'mn469049_db';
    $DB_hostname = 'mn469049.mysql.tools';
    $DB_username = 'mn469049_db';
    $DB_password = 'jPWQQ8U9';
    $DB_keygen = 'm205r1G6NHNs'; //12 values

    $SQL = new SQL ($DB_database, $DB_hostname, $DB_username, $DB_password, $DB_keygen);
    $API = new API('5484985114:AAEhGnuPiLBzTGlxYX8wrIdYgoxlxGDXKg0');

    $text = json_decode(file_get_contents('./config/text.json'), true)['content'];

    $text_keyboard = $text['keyboard'];
    $text_message = $text['message'];

    $input = json_decode(file_get_contents('php://input'), true);
    if (array_key_exists('callback_query', $input)) {
        $data = $input['callback_query'];
        $action = explode (':', explode ('|', $data['data'])[0])[1];
        $type = explode (':', explode ('|', $data['data'])[1])[1];
    } else {
        $data = $input['message'];
    }

    $user_message = $data['text'];
    $user_id = $data['from']['id'];
    $user_first_name = $data['from']['first_name'];
    $user_last_name = $data['from']['last_name'];
    $user_username = $data['from']['username'];
    $message_id = $data['message_id'];

    $sql_result = $SQL->SELECT_FROM('*', 'users', "id = $user_id", NULL);

    if (!$sql_result)
            $SQL->INSERT_INTO('users', 'id, username, first_name, last_name',
                "'$user_id', '$user_username', '$user_first_name', '$user_last_name'");

    if (array_key_exists('callback_query', $input))
        require './query/callback.php';
    else
        require './query/private.php';

    if (array_key_exists('contact', $data)) {
        if ($data['reply_to_message']['text'] == $text_message['welcome']['no_authorize']) {
            $API->deleteMessage($user_id, $data['message_id']);
            $SQL->UPDATE('users',
                "phone_number = '{$data['contact']['phone_number']}'",
                "id = $user_id");

            $sql_result = $SQL->SELECT_FROM('*', 'users', "id = $user_id", NULL);

            $keyboard = new Keyboard('keyboard', false);
            $keyboard = $keyboard->AUTO_CREATE('main_menu', $text_keyboard, $sql_result);

            $callback_sendMessage = $API->sendMessage($user_first_name . $text_message['welcome']['authorize'], $user_id, $keyboard);
            $callback_sendMessage = json_decode($callback_sendMessage, true);

            $SQL->UPDATE('users', "message_id = '{$callback_sendMessage['result']['message_id']}'", "id = $user_id");
            $API->deleteMessage($user_id, $sql_result['message_id']);
        }
    }
    $API->answerCallbackQuery(NULL, NULL, $data['id']);
    $SQL->connect_close();
    file_put_contents('json.json', file_get_contents('php://input'));
}
