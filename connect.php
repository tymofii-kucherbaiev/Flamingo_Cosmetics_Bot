<?php
if (file_get_contents('php://input')) {
    file_put_contents('json.json', file_get_contents('php://input'));
    require $_SERVER['DOCUMENT_ROOT'] . '/config/function.php';
    $text_filling = json_decode(file_get_contents('./config/text.json'), true)['content'];

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
    $keyboard = new keyboard();
    $keyboard->text_filling = $text_filling;

    $input = json_decode(file_get_contents('php://input'), true);

    if (array_key_exists('inline_query', $input)) {
        require $_SERVER['DOCUMENT_ROOT'] . '/query/inline_query.php';
    } else {
        if (array_key_exists('callback_query', $input)) {
            $data = $input['callback_query'];
            $callback_action = explode(':', explode('|', $data['data'])[0])[1];
            $callback_type = explode(':', explode('|', $data['data'])[1])[1];
            $callback_variation = explode(':', explode('|', $data['data'])[2])[1];
        } else {
            $data = $input['message'];
        }

        $user_message = $data['text'];
        $user_id = $data['from']['id'];
        $user_first_name = $data['from']['first_name'];
        $user_last_name = $data['from']['last_name'];
        $user_username = addslashes($data['from']['username']);
        $message_id = $data['message_id'];

        $core->chat_id = $data['from']['id'];

        $mysqli_result_users = $mysqli->query("CALL PC_add_user($user_id, '$user_username', '$user_first_name', '$user_last_name')")->fetch();

        $keyboard->mysqli_result = $mysqli_result_users;
        $keyboard->mysqli_link = $mysqli;

        if (array_key_exists('contact', $data)) {
            if ($data['reply_to_message']['text'] == $text_filling['new_user']) {
                $core->deleteMessage($data['message_id']);

                $mysqli_result_users = $mysqli->query("CALL PC_update_user('phone_number', '{$data['contact']['phone_number']}', '$user_id')")->fetch();
                $keyboard->mysqli_result = $mysqli_result_users;

                $callback = json_decode($core->sendMessage($user_first_name . $text_filling['complete'],
                    $keyboard->main_menu()), true);

                $mysqli->query("CALL PC_update_user('message_id', '{$callback['result']['message_id']}', '$user_id')");
                $core->deleteMessage($mysqli_result_users['message_id']);
            }
        } elseif (array_key_exists('callback_query', $input))
            require $_SERVER['DOCUMENT_ROOT'] . '/query/callback_query.php';
        else
            require $_SERVER['DOCUMENT_ROOT'] . '/query/message_query.php';

//        $core->answerCallbackQuery(NULL, NULL, $data['id']);
    }
}
