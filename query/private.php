<?php
/**
 * @var $data
 * @var $SQL
 * @var $API
 * @var $user_id
 * @var $user_username
 * @var $user_first_name
 * @var $user_last_name
 * @var $text_keyboard
 * @var $text_message
 */

switch ($data['text']) {
    case '/start':
    case 'Назад':
    case 'назад':

    if (!$SQL->SELECT_FROM('*', 'users', "id = $user_id")->num_rows)
        $SQL->INSERT_INTO('users', 'id, username, first_name, last_name, language_code',
            "'$user_id', '$user_username', '$user_first_name', '$user_last_name', '{$data['from']['language_code']}'");

    $keyboard = new Keyboard('keyboard', false);

    $keyboard->add(NULL, $text_keyboard['catalog'], NULL, NULL, 0, 0);
    $i = 0;
    $col = 0;
    $row = 1;
    if (!$SQL->SELECT_FROM('*', 'users', "id = $user_id AND cart IS NOT NULL")->num_rows) {
        $i++;
        $keyboard->add(NULL, $text_keyboard['cart'], NULL, NULL, $row, $col);
        $col++;
    }

    if ($SQL->SELECT_FROM('*', 'users', "id = $user_id AND role = 'administrator'")->num_rows) {
        $i++;
        $keyboard->add(NULL, $text_keyboard['admin'], NULL, NULL, $row, $col);
        $col++;
    }

    if (!$SQL->SELECT_FROM('*', 'users', "id = $user_id AND favorite IS NOT NULL")->num_rows) {
        $i++;
        $keyboard->add(NULL, $text_keyboard['favorite'], NULL, NULL, $row, $col);
        $col++;
    }

    if ($i != 0) $row++;

    if ($SQL->SELECT_FROM('*', 'users', "id = $user_id AND phone_number IS NOT NULL")->num_rows)
        $keyboard->add(NULL, $text_keyboard['profile'], NULL, NULL, $row, 0);
    else
        $keyboard->add('request_contact', $text_keyboard['login'], NULL, true, $row, 0);

    $keyboard->add(NULL, $text_keyboard['help'], NULL, NULL, $row, 1);

    $API->sendMessage($text_message['welcome'], $user_id, $keyboard->get());
    break;

    case '/catalog':

        break;

    case '/cart':

        break;

    case '/account':

        break;

    case '/help':

        break;

    case 'Войти':
    case 'войти':

        $keyboard = new Keyboard('keyboard', false);
        $keyboard->add('request_contact','Войти по номеру телефона', NULL, true, 0, 0);
        $keyboard->add('request_location','Добавить адресс доставки', NULL, true, 0, 1);


        $keyboard->add(NULL,'Назад', NULL, NULL, 1, 0);
        $keyboard->add(NULL,'Пользовательское соглашение', NULL, NULL, 1, 1);

        $API->sendMessage('Добро пожаловать', $user_id, $keyboard->get());

        break;

    default:
//        $API->sendLocation($user_id, '47.9915952', '37.8940774');
        break;
}
