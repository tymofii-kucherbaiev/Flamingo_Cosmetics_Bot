<?php
if (file_get_contents('php://input')) {
    require './config/function.php';

    $DB_database = 'mn469049_db';
    $DB_hostname = 'mn469049.mysql.tools';
    $DB_username = 'mn469049_db';
    $DB_password = 'jPWQQ8U9';
    $DB_keygen = 'm205r1G6NHNs'; //12 values
    $DB_botname = 'Flamingo_Cosmetics_Bot'; //12 values

    $SQL = new SQL ($DB_database, $DB_hostname, $DB_username, $DB_password, $DB_keygen, $DB_botname);
    $API = new API('5484985114:AAEhGnuPiLBzTGlxYX8wrIdYgoxlxGDXKg0');

    $text = json_decode(file_get_contents('./config/text.json'), true)['content'];

    $text_keyboard = $text['keyboard'];
    $text_message = $text['message'];

    $input = json_decode(file_get_contents('php://input'), true);
    if (array_key_exists('callback_query', $input)) {
        $data = $input['callback_query'];
    } else {
        $data = $input['message'];
    }

    $user_message = $data['text'];
    $user_id = $data['from']['id'];
    $user_first_name = $data['from']['first_name'];
    $user_last_name = $data['from']['last_name'];
    $user_username = $data['from']['username'];
    $message_id = $data['message_id'];

    $API->answerCallbackQuery(NULL, NULL, $data['id']);

    $SQL_RESULT = $SQL->SELECT_FROM('*', 'users', "id = $user_id")->fetch_assoc();

    if (!$SQL_RESULT) {
            $SQL->INSERT_INTO('users', 'id, username, first_name, last_name, language_code, favorite, cart_product, role',
                "'$user_id', '$user_username', '$user_first_name', '$user_last_name', '{$data['from']['language_code']}',
                 '4712826232980, 4712826234520, 4712826236451', '4712826232234 [2], 4712826223980 [1], 4712826232980 [3]', 'administrator'");

//        $SQL->INSERT_INTO('users', 'id, username, first_name, last_name, language_code',
//            "'$user_id', '$user_username', '$user_first_name', '$user_last_name', '{$data['from']['language_code']}'");
        $SQL_RESULT = $SQL->SELECT_FROM('*', 'users', "id = $user_id")->fetch_assoc();
    }

    if (array_key_exists('callback_query', $input)) {
        require './query/callback.php';
    } else {
        require './query/private.php';
    }

    if (array_key_exists('contact', $data)) {
        $SQL->UPDATE('users',
            "phone_number = '" . substr($data['contact']['phone_number'], 1) . "'",
            "id = $user_id");
        $SQL_RESULT = $SQL->SELECT_FROM('*', 'users', "id = $user_id")->fetch_assoc();
        if ($data['reply_to_message']['text'] == $text_message['welcome']) {
            $keyboard = new Keyboard('keyboard', false);
            $keyboard = $keyboard->AUTO_CREATE('main_menu', $text_keyboard, $SQL_RESULT);

            $API->sendMessage($user_first_name . ", " . $text_message['welcome_authorize_caption'], $user_id, $keyboard);
        }
    }

    $SQL->connect_close();
    file_put_contents('json.json', file_get_contents('php://input'));
}
