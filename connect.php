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

    $API->answerCallbackQuery(NULL, NULL, $data['id']);

    if (array_key_exists('callback_query', $input)) {
        require './query/callback.php';
    } else {
        require './query/private.php';
    }

    if (array_key_exists('contact', $data)) {
        $SQL->UPDATE('users',
            "phone_number = '" . substr($data['contact']['phone_number'], 1) . "'",
            "id = $user_id");
        if ($data['reply_to_message']['text'] == $text_message['welcome']) {
            $oKeyboard = new Keyboard('keyboard', false);
            $oKeyboard->add(NULL, $text_keyboard['catalog'], NULL, NULL, 0, 0);

            $oKeyboard->add(NULL, $text_keyboard['profile'], NULL, NULL, 1, 0);

            if ($SQL->SELECT_FROM('*', 'users', "id = $user_id AND role = 'administrator'")->num_rows) {
                $oKeyboard->add(NULL, $text_keyboard['admin'], NULL, NULL, 1, 1);
                $oKeyboard->add(NULL, $text_keyboard['help'], NULL, NULL, 1, 2);
            } else {
                $oKeyboard->add(NULL, $text_keyboard['help'], NULL, NULL, 1, 1);
            }

            $keyboard = $oKeyboard->get();
            $API->sendMessage($user_first_name . ", " . $text_message['welcome_authorize_caption'], $user_id, $keyboard);
        }
    }
    $oKeyboard = new Keyboard('keyboard', false);
//    $oKeyboard->add(NULL, $text_keyboard['catalog'], NULL, NULL, 0, 0);

    $oKeyboard->add(NULL, $text_keyboard['login'], NULL, NULL, 0, 0);

//    if ($SQL->SELECT_FROM('*', 'users', "id = $user_id AND role = 'administrator'")->num_rows) {
//        $oKeyboard->add(NULL, $text_keyboard['admin'], NULL, NULL, 1, 1);
//        $oKeyboard->add(NULL, $text_keyboard['help'], NULL, NULL, 1, 2);
//    } else {
        $oKeyboard->add(NULL, $text_keyboard['help'], NULL, NULL, 0, 1);
//    }

    $keyboard = $oKeyboard->get();
//    $API->sendMessage($user_first_name . ", " . $text_message['welcome_authorize_caption'], $user_id, $keyboard);

    $SQL->connect_close();
    file_put_contents('json.json', $input = file_get_contents('php://input'));
}
