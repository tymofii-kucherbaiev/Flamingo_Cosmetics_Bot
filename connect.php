<?php
if (file_get_contents('php://input')) {
    file_put_contents('json.json', file_get_contents('php://input'));
    require $_SERVER['DOCUMENT_ROOT'] . '/config/function.php';

    $db_hostname = 'mn469049.mysql.tools';
    $db_database = 'mn469049_db';
    $db_username = 'mn469049_db';
    $db_password = 'jPWQQ8U9';

    $mysqli_option = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    );
    $mysqli = new PDO("mysql:host=$db_hostname;dbname=$db_database", $db_username, $db_password,
        $mysqli_option);

    $core = new API('5484985114:AAEhGnuPiLBzTGlxYX8wrIdYgoxlxGDXKg0');

    $text_filling = json_decode(file_get_contents('./config/message_control.json'), true)['content'];
    $input = json_decode(file_get_contents('php://input'), true);

    $keyboard = new keyboard($text_filling);


    $bool_inline_query = array_key_exists('inline_query', $input);
    $bool_callback_query = array_key_exists('callback_query', $input);

    if ($bool_inline_query === TRUE) {
        $data = $input['inline_query'];
    } elseif ($bool_callback_query === TRUE) {
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
    $mysqli_result_users = $mysqli->query("CALL PC_user($user_id, '$user_username', '$user_first_name', '$user_last_name')")->fetch();

    $keyboard->mysqli_result = $mysqli_result_users;
    $keyboard->mysqli_link = $mysqli;

    if ($bool_inline_query === TRUE) {
        require $_SERVER['DOCUMENT_ROOT'] . '/query/inline_query.php';
    } elseif ($bool_callback_query === TRUE) {
        require $_SERVER['DOCUMENT_ROOT'] . '/query/callback_query.php';
    } else {
        require $_SERVER['DOCUMENT_ROOT'] . '/query/message_query.php';
    }
}
