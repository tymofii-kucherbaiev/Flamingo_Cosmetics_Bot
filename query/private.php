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

        $oKeyboard = new Keyboard('keyboard', false);
        $oKeyboard->add(NULL, $text_keyboard['catalog'], 'a', 'a', 0, 0);


        if ($SQL->SELECT_FROM('*', 'users', "id = $user_id AND phone_number IS NOT NULL")->num_rows)
            $oKeyboard->add(NULL, $text_keyboard['profile'], 'a', 'a', 1, 0);
        else
            $oKeyboard->add(NULL, $text_keyboard['login'], 'a', 'a', 1, 0);
        if ($SQL->SELECT_FROM('*', 'users', "id = $user_id AND role = 'administrator'")->num_rows) {
            $oKeyboard->add(NULL, $text_keyboard['admin'], 'a', 'a', 1, 1);
            $oKeyboard->add(NULL, $text_keyboard['help'], 'a', 'a', 1, 2);
        } else {
            $oKeyboard->add(NULL, $text_keyboard['help'], 'a', 'a', 1, 1);
        }

        $keyboard = $oKeyboard->get();
        $API->sendMessage($text_message['welcome'], $user_id, $keyboard);
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

        $oKeyboard = new Keyboard('keyboard', false);
        $oKeyboard->add('request_contact','Войти по номеру телефона', NULL, true, 0, 0);
        $oKeyboard->add('request_location','Добавить адресс доставки', NULL, true, 0, 1);


        $oKeyboard->add(NULL,'Назад', NULL, NULL, 1, 0);
        $oKeyboard->add(NULL,'Пользовательское соглашение', NULL, NULL, 1, 1);

        $keyboard = $oKeyboard->get();
        $API->sendMessage('Добро пожаловать', $user_id, $keyboard);

        break;

    default:
//        $API->sendLocation($user_id, '47.9915952', '37.8940774');
        break;
}
