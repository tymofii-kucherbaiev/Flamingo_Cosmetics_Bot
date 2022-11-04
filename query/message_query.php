<?php
/**
 * @var $mysqli_result_users mysqli_result
 * @var $mysqli mysqli_result
 * @var $core API
 * @var $keyboard keyboard
 * @var $data
 * @var $user_id integer
 * @var $text_filling array
 */

$core->deleteMessage($data['message_id']);
switch ($data['text']) {
    case $text_filling['command']['start']:

        if ($mysqli_result_users['phone_number']) $message = $mysqli_result_users['first_name'] . $text_filling['message']['welcome'];
        else $message = $text_filling['message']['new_user'];

        $callback = json_decode($core->sendMessage($message, $keyboard->main_menu()), true);

        $mysqli->query("CALL PC_update_user('message_id', '{$callback['result']['message_id']}', '$user_id')");

        $core->deleteMessage($mysqli_result_users['message_id']);
        $core->deleteMessage($mysqli_result_users['callback_id']);
        break;

    case $text_filling['command']['search']:
    case $text_filling['keyboard']['main']['search']:
        $keyboard->keyboard_type = 'inline_keyboard';

        $callback = json_decode($core->sendMessage($text_filling['message']['search'], $keyboard->search_main_menu()), true);
        $mysqli->query("CALL PC_update_user('callback_id', '{$callback['result']['message_id']}', '$user_id')");
        $core->deleteMessage($mysqli_result_users['callback_id']);
        break;


}





//switch ($data['text']) {
//    case $text_keyboard['main_admin']:
//        if ($mysqli_result_users['role'] == 'administrator') {
//            $core->deleteMessage($data['message_id']);
//            $keyboard = new keyboard('inline_keyboard', false);
//            $keyboard = $keyboard->auto_create('admin_main', $text_keyboard, $mysqli_result_users, NULL, NULL);
//
////            $callback_sendMessage = $core->sendMessage($text_message['welcome'], $user_id, $keyboard);
////            $callback_sendMessage = json_decode($callback_sendMessage, true);
////            $SQL->UPDATE('users', "callback_id = '{$callback_sendMessage['result']['message_id']}'", "id = $user_id");
//            $core->deleteMessage($mysqli_result_users['message_id']);
//            $core->deleteMessage($mysqli_result_users['callback_id']);
//        }
//        break;
//
//    case '/start':
//        $core->deleteMessage($data['message_id']);
//        $keyboard = new keyboard('keyboard', false);
//        $keyboard = $keyboard->auto_create('main_menu', $text_keyboard, $mysqli_result_users, NULL, NULL);
//
//        if ($mysqli_result_users['phone_number']) $message = $mysqli_result_users['first_name'] . $text_message['welcome']['general'];
//        else $message = $text_message['welcome']['no_authorize'];
//
//        $callback_sendMessage = json_decode(
//            $core->sendMessage($message, $keyboard, NULL), true);
//        $mysqli->query("CALL PC_update_user('message_id', '{$callback_sendMessage['result']['message_id']}', '$user_id')");
//        $core->deleteMessage($mysqli_result_users['message_id']);
//        $core->deleteMessage($mysqli_result_users['callback_id']);
//        break;
//
//    case $text_keyboard['main_profile']:
//    case '/account':
//        $core->deleteMessage($data['message_id']);
//        $keyboard = new keyboard('inline_keyboard', false);
//        $keyboard = $keyboard->auto_create('user_account', $text_keyboard, $mysqli_result_users, NULL, NULL);
//
//        $callback_sendMessage = json_decode(
//            $core->sendMessage($text_message['welcome'], $keyboard, NULL), true);
//        $SQL->UPDATE('users', "callback_id = '{$callback_sendMessage['result']['message_id']}'", "id = $user_id");
//        $core->deleteMessage($mysqli_result_users['callback_id']);
//        break;
//
//    case $text_keyboard['main_search']:
//    case '/catalog':
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
//        $keyboard = new keyboard('inline_keyboard', false);
//        $keyboard = $keyboard->AUTO_CREATE('test', $text_keyboard, $result);
//
//
//        $core->sendMessage($text_message['welcome']['general'], $user_id, $keyboard);
//        break;
//
//    default:
//        $core->deleteMessage($user_id, $data['message_id']);
//        break;
//}


//switch ($data['text']) {
//    case '/account':
//    case $text_keyboard['main_profile']:
//
//
//        $message_id = $SQL->SELECT_FROM('message_id, phone_number', "users", "id = '$user_id'", NULL)->fetch_assoc();
//        $core->deleteMessage($user_id, $data['message_id']);
//        $core->deleteMessage($user_id, $message_id['message_id']);
//        $keyboard = new keyboard('keyboard', false);
//        if ($message_id['phone_number']) {
//            $keyboard = $keyboard->AUTO_CREATE('user_account', $text_keyboard, $SQL_result);
//
////            $core->editMessageText($text_message['welcome'] . '1', $user_id, 1040, NULL);
//            $core->sendMessage($text_message['welcome'], $user_id, $keyboard);
//        } else {
//            $keyboard = $keyboard->AUTO_CREATE('main_menu', $text_keyboard, $SQL_result);
//
////            $core->editMessageText($text_message['welcome'] . '2', $user_id, $message_id['message_id'], NULL);
//            $core->sendMessage($text_message['welcome'] . 'ошибка, авторизироваться снова', $user_id, $keyboard);
//        }
//        break;
//
//    case '/help':
//
//        break;
//
//    default:
////        $core->sendLocation($user_id, '47.9915952', '37.8940774');
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
//        $keyboard = new keyboard('NULL', false);
//        $keyboard = $keyboard->AUTO_CREATE('test', $text_keyboard, $result);
//
//
//
//            $core->sendMessage($text_message['welcome'], $user_id, $keyboard);
//
//        break;
//}


