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

    if (!$SQL->SELECT_FROM('*', 'users', "id = 445891579")->num_rows)
        $SQL->INSERT_INTO('users', 'id, username, first_name, last_name, language_code, favorite, cart_product, role',
            "'$user_id', '$user_username', '$user_first_name', '$user_last_name', '{$data['from']['language_code']}',
             '4712826232980, 4712826234520, 4712826236451', '4712826232234 [2], 4712826223980 [1], 4712826232980 [3]', 'administrator'");

    if (!$SQL->SELECT_FROM('*', 'users', "id = $user_id")->num_rows)
        $SQL->INSERT_INTO('users', 'id, username, first_name, last_name, language_code',
            "'$user_id', '$user_username', '$user_first_name', '$user_last_name', '{$data['from']['language_code']}'");

//    $keyboard = new Keyboard('keyboard', false);
//
//    $keyboard->add(NULL, $text_keyboard['catalog'], NULL, NULL, 0, 0);
//
//    $i = 0; $col = 0; $row = 1;
//
//    if ($SQL->SELECT_FROM('*', 'users', "id = $user_id AND cart_product IS NOT NULL")->num_rows) {
//        $i++;
//        $keyboard->add(NULL, $text_keyboard['cart'], NULL, NULL, $row, $col);
//        $col++;
//    }
//
//    if ($SQL->SELECT_FROM('*', 'users', "id = $user_id AND role = 'administrator'")->num_rows) {
//        $i++;
//        $keyboard->add(NULL, $text_keyboard['admin'], NULL, NULL, $row, $col);
//        $col++;
//    }
//
//    if ($SQL->SELECT_FROM('*', 'users', "id = $user_id AND favorite IS NOT NULL")->num_rows) {
//        $i++;
//        $keyboard->add(NULL, $text_keyboard['favorite'], NULL, NULL, $row, $col);
//        $col++;
//    }
//
//    if ($i != 0) $row++;
//
//    if ($SQL->SELECT_FROM('*', 'users', "id = $user_id AND phone_number IS NOT NULL")->num_rows)
//        $keyboard->add(NULL, $text_keyboard['profile'], NULL, NULL, $row, 0);
//    else
//        $keyboard->add('request_contact', $text_keyboard['login'], NULL, true, $row, 0);
//
//    $keyboard->add(NULL, $text_keyboard['help'], NULL, NULL, $row, 1);

    $keyboard = new Keyboard('keyboard', false);
    $keyboard = $keyboard->AUTO_CREATE('main_menu', $text_keyboard, $user_id, $SQL);

    $API->sendMessage($text_message['welcome'], $user_id, $keyboard);
    break;

    case '/catalog':

        break;

    case '/account':

        break;

    case '/help':

        break;

    default:
//        $API->sendLocation($user_id, '47.9915952', '37.8940774');
        break;
}
