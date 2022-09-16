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
 * @var $SQL_RESULT
 */

switch ($data['text']) {
    case '/start':
    case $text_keyboard['main_back']:

        if (!$SQL_RESULT) {
//            $SQL->INSERT_INTO('users', 'id, username, first_name, last_name, language_code, favorite, cart_product, role',
//                "'$user_id', '$user_username', '$user_first_name', '$user_last_name', '{$data['from']['language_code']}',
//                 '4712826232980, 4712826234520, 4712826236451', '4712826232234 [2], 4712826223980 [1], 4712826232980 [3]', 'administrator'");

            $SQL->INSERT_INTO('users', 'id, username, first_name, last_name, language_code',
                "'$user_id', '$user_username', '$user_first_name', '$user_last_name', '{$data['from']['language_code']}'");
        }

        $keyboard = new Keyboard('keyboard', false);
        $keyboard = $keyboard->AUTO_CREATE('main_menu', $text_keyboard, $SQL_RESULT);


        $API->sendMessage($text_message['welcome'], $user_id, $keyboard);
    break;

    case '/catalog':

        break;

    case '/account':
    case $text_keyboard['main_profile']:
        if ($SQL_RESULT['phone_number']) {
            $keyboard = new Keyboard('keyboard', false);
            $keyboard = $keyboard->AUTO_CREATE('user_account', $text_keyboard, $SQL_RESULT);

            $API->sendMessage($text_message['welcome'], $user_id, $keyboard);
        } else {
            $keyboard = new Keyboard('keyboard', false);
            $keyboard = $keyboard->AUTO_CREATE('user_account', $text_keyboard, $SQL_RESULT);

            $API->sendMessage($text_message['welcome'] . 'ошибка, авторизироваться снова', $user_id, $keyboard);
        }
        break;

    case '/help':

        break;

    default:
//        $API->sendLocation($user_id, '47.9915952', '37.8940774');
        break;
}
