<?php
/**
 * @var $sql_result_user
 * @var $mysqli
 * @var $core API
 * @var $data
 * @var $text_message
 * @var $text_keyboard
 */

switch ($data['text']) {
    case '/start':
        $core->deleteMessage($data['message_id']);


        break;

    case '':

        break;

}





switch ($data['text']) {
    case $text_keyboard['main_admin']:
        if ($sql_result_user['role'] == 'administrator') {
            $core->deleteMessage($data['message_id']);
            $keyboard = new Keyboard('inline_keyboard', false);
            $keyboard = $keyboard->auto_create('admin_main', $text_keyboard, $sql_result_user, NULL, NULL);

//            $callback_sendMessage = $core->sendMessage($text_message['welcome'], $user_id, $keyboard);
//            $callback_sendMessage = json_decode($callback_sendMessage, true);
//            $SQL->UPDATE('users', "callback_id = '{$callback_sendMessage['result']['message_id']}'", "id = $user_id");
            $core->deleteMessage($sql_result_user['message_id']);
            $core->deleteMessage($sql_result_user['callback_id']);
        }
        break;

    case '/start':
        $core->deleteMessage($data['message_id']);
        $keyboard = new Keyboard('keyboard', false);
        $keyboard = $keyboard->auto_create('main_menu', $text_keyboard, $sql_result_user, NULL, NULL);

        if ($sql_result_user['phone_number']) $message = $sql_result_user['first_name'] . $text_message['welcome']['general'];
        else $message = $text_message['welcome']['no_authorize'];

        $callback_sendMessage = json_decode(
            $core->sendMessage($message, $keyboard, NULL), true);
        $mysqli->query("CALL PC_update_user('message_id', '{$callback_sendMessage['result']['message_id']}', '$user_id')");
        $core->deleteMessage($sql_result_user['message_id']);
        $core->deleteMessage($sql_result_user['callback_id']);
        break;

    case $text_keyboard['main_profile']:
    case '/account':
        $core->deleteMessage($data['message_id']);
        $keyboard = new Keyboard('inline_keyboard', false);
        $keyboard = $keyboard->auto_create('user_account', $text_keyboard, $sql_result_user, NULL, NULL);

        $callback_sendMessage = json_decode(
            $core->sendMessage($text_message['welcome'], $keyboard, NULL), true);
        $SQL->UPDATE('users', "callback_id = '{$callback_sendMessage['result']['message_id']}'", "id = $user_id");
        $core->deleteMessage($sql_result_user['callback_id']);
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


        $core->sendMessage($text_message['welcome']['general'], $user_id, $keyboard);
        break;

    default:
        $core->deleteMessage($user_id, $data['message_id']);
        break;
}











//switch ($data['text']) {
//    case '/start':
//    case $text_keyboard['main_back']:
//        $keyboard = new Keyboard('keyboard', false);
//        $keyboard = $keyboard->AUTO_CREATE('main_menu', $text_keyboard, $SQL_result);
//
//        $callback_result = $core->sendMessage($text_message['welcome'], $user_id, $keyboard);
//        $callback_result = json_decode($callback_result, true);
//        $SQL->UPDATE('users', "message_id = '{$callback_result['result']['message_id']}'", "id = $user_id");
//    break;
//
//    case '/search':
//    case $text_keyboard['main_search']:
//        $keyboard = new Keyboard('inline_keyboard', false);
//        $keyboard = $keyboard->AUTO_CREATE('catalog_search', $text_keyboard, $SQL);
//
//        $core->sendMessage($text_message['welcome'], $user_id, $keyboard);
//        break;
//
//    case '/account':
//    case $text_keyboard['main_profile']:
//
//
//        $message_id = $SQL->SELECT_FROM('message_id, phone_number', "users", "id = '$user_id'", NULL)->fetch_assoc();
//        $core->deleteMessage($user_id, $data['message_id']);
//        $core->deleteMessage($user_id, $message_id['message_id']);
//        $keyboard = new Keyboard('keyboard', false);
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
//        $keyboard = new Keyboard('NULL', false);
//        $keyboard = $keyboard->AUTO_CREATE('test', $text_keyboard, $result);
//
//
//
//            $core->sendMessage($text_message['welcome'], $user_id, $keyboard);
//
//        break;
//}


