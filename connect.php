<?php
if (file_get_contents('php://input')) {
    /**
     * @var string $DB_database
     * @var string $DB_hostname
     * @var string $DB_username
     * @var string $DB_password
     * @var string $DB_keygen
     * @var string $DB_botname
     */

    require './config/function.php';
    require './config/config.php';

    $SQL = new SQL ($DB_database, $DB_hostname, $DB_username, $DB_password, $DB_keygen, $DB_botname);
    $API = new API('5322180222:AAHzWzIqD3XEvJcvV28xo-Fd56oo-H8SAiU');

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

    if (array_key_exists('callback_query', $input)) {
        require './query/callback.php';
        $API->sendMessage('Hello', $user_id, $keyboard);
        $API->answerCallbackQuery(NULL, NULL, $data['id']);
    } else {
        $API->sendMessage('Hello', $user_id, $keyboard);
        require './query/private.php';
    }
    $SQL->connect_close();
}
