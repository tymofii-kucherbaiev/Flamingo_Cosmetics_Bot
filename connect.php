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

    if ($message = '/start')
        if (!$SQL->SELECT_FROM('*', 'users', "id = $user_id")->num_rows)
            $SQL->INSERT_INTO('users', 'id, username, first_name, last_name, language_code',
                "'$user_id', '$user_username', '$user_first_name', '$user_last_name', '{$data['from']['language_code']}'");


    $oKeyboard = new Keyboard('keyboard', true);
    $oKeyboard->add(' Каталог', 'a', 'a', 0, 0);


    if ($SQL->SELECT_FROM('*', 'users', "id = $user_id AND phone_number IS NOT NULL")->num_rows)
        $oKeyboard->add(' Кабинет', 'a', 'a', 0, 1);
    else
        $oKeyboard->add(' Войти', 'a', 'a', 0, 1);


    $oKeyboard->add(' Заказы', 'a', 'a', 1, 0);
    $oKeyboard->add(' Помощь', 'a', 'a', 1, 1);
//$oKeyboard->add(' English', 'a', 'a', 2, 0);
//$oKeyboard->add(' Deutsch', 'a', 'a', 3, 1);
    $keyboard = $oKeyboard->get();


    $API->sendMessage('Hello', $user_id, $keyboard);


    if (array_key_exists('callback_query', $input)) {
        require './query/callback.php';
    } else {
        require './query/private.php';
    }
    $SQL->connect_close();
}
