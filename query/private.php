<?php
/**
 * @var $data
 * @var $SQL
 * @var $API
 * @var $user_id
 * @var $user_username
 * @var $user_first_name
 * @var $user_last_name
 */

switch ($data['text']) {
    case '/start':
        if (!$SQL->SELECT_FROM('*', 'users', "id = $user_id")->num_rows)
            $SQL->INSERT_INTO('users', 'id, username, first_name, last_name, language_code',
                "'$user_id', '$user_username', '$user_first_name', '$user_last_name', '{$data['from']['language_code']}'");

//        $oKeyboard = new Keyboard('keyboard', false);
//        $oKeyboard->add('Каталог', 'a', 'a', 0, 0);
//
//
//        if ($SQL->SELECT_FROM('*', 'users', "id = $user_id AND phone_number IS NOT NULL")->num_rows)
//            $oKeyboard->add('Кабинет', 'a', 'a', 0, 1);
//        else
//            $oKeyboard->add('Войти', 'a', 'a', 0, 1);
//
//
//        $oKeyboard->add('Заказы', 'a', 'a', 1, 0);
//        $oKeyboard->add('Помощь', 'a', 'a', 1, 1);
//$oKeyboard->add(' English', 'a', 'a', 2, 0);
//$oKeyboard->add(' Deutsch', 'a', 'a', 3, 1);
//        $keyboard = $oKeyboard->get();
//        $API->sendMessage('Hello', $user_id, $keyboard);
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
        $API->sendMessage('Hellыo', $user_id, $keyboard);
        break;

    default:
        $API->sendLocation($user_id, '50.516682', '30.793097');
        break;
}
