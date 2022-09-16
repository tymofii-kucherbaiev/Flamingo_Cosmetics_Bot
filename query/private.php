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
 * @var $SQL_result
 */

switch ($data['text']) {
    case '/start':
    case $text_keyboard['main_back']:

        $keyboard = new Keyboard('keyboard', false);
        $keyboard = $keyboard->AUTO_CREATE('main_menu', $text_keyboard, $SQL_result);


        $API->sendMessage($text_message['welcome'], $user_id, $keyboard);
    break;

    case '/catalog':
    case $text_keyboard['main_catalog']:
        $keyboard = new Keyboard('inline_keyboard', false);
        $keyboard = $keyboard->AUTO_CREATE('catalog', $text_keyboard, $SQL);

        $API->sendMessage($text_message['welcome'], $user_id, $keyboard);

        break;

    case '/account':
    case $text_keyboard['main_profile']:
        $keyboard = new Keyboard('keyboard', false);
        if ($SQL_result['phone_number']) {
            $keyboard = $keyboard->AUTO_CREATE('user_account', $text_keyboard, $SQL_result);

            $API->sendMessage($text_message['welcome'], $user_id, $keyboard);
        } else {
            $keyboard = $keyboard->AUTO_CREATE('main_menu', $text_keyboard, $SQL_result);

            $API->sendMessage($text_message['welcome'] . 'ошибка, авторизироваться снова', $user_id, $keyboard);
        }
        break;

    case '/help':

        break;

    default:
//        $API->sendLocation($user_id, '47.9915952', '37.8940774');
        break;
}
