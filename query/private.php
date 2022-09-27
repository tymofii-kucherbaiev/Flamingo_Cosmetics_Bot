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
 * @var $sql_result
 */

switch ($data['text']) {
    case $text_keyboard['main_admin']:
        if ($sql_result['role'] == 'administrator') {
            $API->deleteMessage($user_id, $data['message_id']);
            $keyboard = new Keyboard('inline_keyboard', false);
            $keyboard = $keyboard->AUTO_CREATE('admin_main', $text_keyboard, $sql_result);

            $callback_sendMessage = $API->sendMessage($text_message['welcome'], $user_id, $keyboard);
            $callback_sendMessage = json_decode($callback_sendMessage, true);
            $SQL->UPDATE('users', "callback_id = '{$callback_sendMessage['result']['message_id']}'", "id = $user_id");
//            $API->deleteMessage($user_id, $sql_result['message_id']);
            $API->deleteMessage($user_id, $sql_result['callback_id']);
        }
        break;


    case '/start':
        $API->deleteMessage($user_id, $data['message_id']);
        $keyboard = new Keyboard('keyboard', false);
        $keyboard = $keyboard->AUTO_CREATE('main_menu', $text_keyboard, $sql_result);


        if ($sql_result['phone_number']) {
            $message = $sql_result['first_name']  . $text_message['welcome']['general'];
        } else {
            $message = $text_message['welcome']['no_authorize'];
        }


        $callback_sendMessage = $API->sendMessage($message, $user_id, $keyboard);
        $callback_sendMessage = json_decode($callback_sendMessage, true);
        $SQL->UPDATE('users', "message_id = '{$callback_sendMessage['result']['message_id']}'", "id = $user_id");
        $API->deleteMessage($user_id, $sql_result['message_id']);
        $API->deleteMessage($user_id, $sql_result['callback_id']);
        break;

    case $text_keyboard['main_profile']:
    case '/account':
        $API->deleteMessage($user_id, $data['message_id']);
        $keyboard = new Keyboard('inline_keyboard', false);
        $keyboard = $keyboard->AUTO_CREATE('user_account', $text_keyboard, $sql_result);

        $callback_sendMessage = $API->sendMessage($text_message['welcome'], $user_id, $keyboard);
        $callback_sendMessage = json_decode($callback_sendMessage, true);
        $SQL->UPDATE('users', "callback_id = '{$callback_sendMessage['result']['message_id']}'", "id = $user_id");
//        $API->deleteMessage($user_id, $sql_result['message_id']);
        $API->deleteMessage($user_id, $sql_result['callback_id']);
        break;

    case $text_keyboard['main_search']:
    case '/catalog':
            $result = $SQL->link()->query("SELECT brand.description AS 'Brand',

(SELECT GROUP_CONCAT(category.description ORDER BY category.category_count ASC SEPARATOR ', ')
FROM category, brand_category
WHERE category.id = brand_category.category_id and brand.id = brand_category.brand_id) AS 'Category'

FROM brand_category
INNER JOIN brand ON (brand.id = brand_category.brand_id)
#LEFT JOIN category ON (category.id = brand_category.category_id)

GROUP BY brand.description
ORDER BY brand.brand_count ASC");

        $keyboard = new Keyboard('inline_keyboard', false);
        $keyboard = $keyboard->AUTO_CREATE('test', $text_keyboard, $result);



            $API->sendMessage($text_message['welcome']['general'], $user_id, $keyboard);
        break;

    default:
        $API->deleteMessage($user_id, $data['message_id']);
        break;
}











//switch ($data['text']) {
//    case '/start':
//    case $text_keyboard['main_back']:
//        $keyboard = new Keyboard('keyboard', false);
//        $keyboard = $keyboard->AUTO_CREATE('main_menu', $text_keyboard, $SQL_result);
//
//        $callback_result = $API->sendMessage($text_message['welcome'], $user_id, $keyboard);
//        $callback_result = json_decode($callback_result, true);
//        $SQL->UPDATE('users', "message_id = '{$callback_result['result']['message_id']}'", "id = $user_id");
//    break;
//
//    case '/search':
//    case $text_keyboard['main_search']:
//        $keyboard = new Keyboard('inline_keyboard', false);
//        $keyboard = $keyboard->AUTO_CREATE('catalog_search', $text_keyboard, $SQL);
//
//        $API->sendMessage($text_message['welcome'], $user_id, $keyboard);
//        break;
//
//    case '/account':
//    case $text_keyboard['main_profile']:
//
//
//        $message_id = $SQL->SELECT_FROM('message_id, phone_number', "users", "id = '$user_id'", NULL)->fetch_assoc();
//        $API->deleteMessage($user_id, $data['message_id']);
//        $API->deleteMessage($user_id, $message_id['message_id']);
//        $keyboard = new Keyboard('keyboard', false);
//        if ($message_id['phone_number']) {
//            $keyboard = $keyboard->AUTO_CREATE('user_account', $text_keyboard, $SQL_result);
//
////            $API->editMessageText($text_message['welcome'] . '1', $user_id, 1040, NULL);
//            $API->sendMessage($text_message['welcome'], $user_id, $keyboard);
//        } else {
//            $keyboard = $keyboard->AUTO_CREATE('main_menu', $text_keyboard, $SQL_result);
//
////            $API->editMessageText($text_message['welcome'] . '2', $user_id, $message_id['message_id'], NULL);
//            $API->sendMessage($text_message['welcome'] . 'ошибка, авторизироваться снова', $user_id, $keyboard);
//        }
//        break;
//
//    case '/help':
//
//        break;
//
//    default:
////        $API->sendLocation($user_id, '47.9915952', '37.8940774');
//
//        $result = $SQL->link()->query("SELECT brand.description AS 'Brand',
//
//(SELECT GROUP_CONCAT(category.description ORDER BY category.category_count ASC SEPARATOR ', ')
//FROM category, brand_category
//WHERE category.id = brand_category.category_id and brand.id = brand_category.brand_id) AS 'Category'
//
//FROM brand_category
//INNER JOIN brand ON (brand.id = brand_category.brand_id)
//#LEFT JOIN category ON (category.id = brand_category.category_id)
//
//GROUP BY brand.description
//ORDER BY brand.brand_count ASC");
//
//        $keyboard = new Keyboard('NULL', false);
//        $keyboard = $keyboard->AUTO_CREATE('test', $text_keyboard, $result);
//
//
//
//            $API->sendMessage($text_message['welcome'], $user_id, $keyboard);
//
//        break;
//}


