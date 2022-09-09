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



    $data = json_decode(file_get_contents('php://input'), true);

    if (array_key_exists('callback_query', $data)) {
        $data = $data['callback_query'];
    } else {
        $data = $data['message'];
    }

    $message = $data['message']['text'];
    $data_from = $data['message']['from'];

    if ($message = '/start')
        if (!$SQL->SELECT_FROM('*', 'users', "id = {$data['message']['from']['id']}")->num_rows)
            $SQL->INSERT_INTO('users', 'id, username, first_name, last_name, language_code',
                "'{$data_from['id']}', '{$data_from['username']}', '{$data_from['first_name']}', '{$data_from['last_name']}',
            '{$data_from['language_code']}'");


    $oKeyboard = new Keyboard('keyboard', true);
    $oKeyboard->add(' Каталог', 'a', 'a', 0, 0);


    if ($SQL->SELECT_FROM('*', 'users', "id = {$data['message']['from']['id']} AND phone_number IS NOT NULL")->num_rows)
        $oKeyboard->add(' Кабинет', 'a', 'a', 0, 1);
    else
        $oKeyboard->add(' Войти', 'a', 'a', 0, 1);


    $oKeyboard->add(' Заказы', 'a', 'a', 1, 0);
    $oKeyboard->add(' Помощь', 'a', 'a', 1, 1);
//$oKeyboard->add(' English', 'a', 'a', 2, 0);
//$oKeyboard->add(' Deutsch', 'a', 'a', 3, 1);
    $keyboard = $oKeyboard->get();


    $API->sendMessage('Hello', $data['message']['from']['id'], $keyboard);
    file_put_contents('json.json', $data = file_get_contents('php://input'));














    $SQL->connect_close();
}
