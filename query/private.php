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

        $callback_result = $API->sendMessage($text_message['welcome'], $user_id, NULL);
        $callback_result = json_decode($callback_result, true);
        $SQL->UPDATE('users', "message_id = '{$callback_result['result']['message_id']}'", "id = $user_id");
    break;

    case '/search':
    case $text_keyboard['main_search']:
        $keyboard = new Keyboard('inline_keyboard', false);
        $keyboard = $keyboard->AUTO_CREATE('catalog_search', $text_keyboard, $SQL);

        $API->sendMessage($text_message['welcome'], $user_id, $keyboard);
        break;

    case '/account':
    case $text_keyboard['main_profile']:
//        $message_id = $SQL->SELECT_FROM('message_id')
//        $keyboard = new Keyboard('keyboard', false);
//        if ($SQL_result['phone_number']) {
//            $keyboard = $keyboard->AUTO_CREATE('user_account', $text_keyboard, $SQL_result);
//
//            $API->sendMessage($text_message['welcome'], $user_id, $keyboard);
//        } else {
//            $keyboard = $keyboard->AUTO_CREATE('main_menu', $text_keyboard, $SQL_result);
//
//            $API->sendMessage($text_message['welcome'] . 'ошибка, авторизироваться снова', $user_id, $keyboard);
//        }


//        $res = $API->sendMessage($text_message['welcome'] . $data['message_id'], $user_id, NULL);
//        $res = json_decode($res, true);

//        $message_id = $SQL->SELECT_FROM('last_message_get', "users", "id = '$user_id'", NULL)->fetch_assoc();

        $API->editMessageText($text_message['welcome'] . '2', $user_id, 925, NULL);
        break;

    case '/help':

        break;

    default:
//        $API->sendLocation($user_id, '47.9915952', '37.8940774');

        $result = $SQL->link()->query("SELECT brand.description AS 'Brand',

(SELECT GROUP_CONCAT(category.description ORDER BY category.category_count ASC SEPARATOR ', ') 
FROM category, brand_category 
WHERE category.id = brand_category.category_id and brand.id = brand_category.brand_id) AS 'Category'

FROM brand_category
INNER JOIN brand ON (brand.id = brand_category.brand_id)
#LEFT JOIN category ON (category.id = brand_category.category_id)

GROUP BY brand.description
ORDER BY brand.brand_count ASC");

        $keyboard = new Keyboard('NULL', false);
        $keyboard = $keyboard->AUTO_CREATE('test', $text_keyboard, $result);



            $API->sendMessage($text_message['welcome'], $user_id, $keyboard);

        break;
}
//$API->deleteMessage($user_id, $data['message_id']);
