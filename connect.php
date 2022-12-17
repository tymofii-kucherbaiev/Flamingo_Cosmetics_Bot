<?php
/**
 * @var $db_hostname string
 * @var $db_database string
 * @var $db_username string
 * @var $db_password string
 * @var $local_access_token string
 */

if (file_get_contents('php://input')) {
    require $_SERVER['DOCUMENT_ROOT'] . '/config/function.php';
    require $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';


    $text_filling = json_decode(file_get_contents('./json/message_control.json'), true)['content'];
    $input = json_decode(file_get_contents('php://input'), true);
    $profile_order = json_decode(file_get_contents('./json/order_comment.json'), true);

    $mysqli_option = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    );
    $mysqli = new PDO("mysql:host=$db_hostname;dbname=$db_database", $db_username, $db_password,
        $mysqli_option);

    $function = new other();
    $function->mysqli_link = $mysqli;
    $function->text_filling = $text_filling;

    $core = new api($local_access_token);
    $core->parse_mode = 'html';


    $bool_inline_query = array_key_exists('inline_query', $input);
    $bool_callback_query = array_key_exists('callback_query', $input);

    if ($bool_inline_query === TRUE) {
        $data = $input['inline_query'];
    } elseif ($bool_callback_query === TRUE) {
        $data = $input['callback_query'];
        $message_id = $data['message']['message_id'];
        $inline_keyboard = $data['message']['reply_markup']['inline_keyboard'];

        $callback_action = explode(':', explode('|', $data['data'])[0])[1];
        $callback_type = explode(':', explode('|', $data['data'])[1])[1];
        $callback_variation = explode(':', explode('|', $data['data'])[2])[1];
    } else {
        $data = $input['message'];
        $message_id = $data['message_id'];

        $bool_via_bot = array_key_exists('via_bot', $data);
    }

    $user_message = $data['text'];
    $user_id = $data['from']['id'];
    $user_first_name = $data['from']['first_name'];
    $user_last_name = $data['from']['last_name'];
    $user_username = addslashes($data['from']['username']);

    $core->chat_id = $user_id;
    $mysqli_result_users = $mysqli->query("CALL PC_user($user_id, '$user_username', '$user_first_name', '$user_last_name')")->fetch();


    if ($user_id == 445891579)
        file_put_contents('json.json', file_get_contents('php://input'));


    $keyboard = new keyboard($text_filling);
    $keyboard->mysqli_result = $mysqli_result_users;
    $keyboard->mysqli_link = $mysqli;
    $keyboard->user_id = $user_id;

    if ($bool_inline_query === TRUE) {
        require $_SERVER['DOCUMENT_ROOT'] . '/query/inline_query.php';
    } elseif ($bool_callback_query === TRUE) {
        require $_SERVER['DOCUMENT_ROOT'] . '/query/callback_query.php';
    } else {
        require $_SERVER['DOCUMENT_ROOT'] . '/query/message_query.php';
    }
}
