<?php
/**
 * @var $data
 * @var $SQL
 * @var $core
 * @var $user_id
 * @var $user_username
 * @var $user_first_name
 * @var $user_last_name
 * @var $text_keyboard
 * @var $text_message
 * @var $sql_result
 * @var $action
 * @var $sql_result_user
 * @var $mysqli
 * @var $core API
 */


switch ($action) {
    case 'close':
        $core->deleteMessage($data['message_id']);
        $keyboard = new Keyboard('keyboard', false);
        $keyboard = $keyboard->auto_create('main_menu', $text_keyboard, $sql_result_user, NULL, NULL);

        if ($sql_result_user['phone_number'])
            $message = $sql_result_user['first_name'] . $text_message['welcome']['general'];
        else
            $message = $text_message['welcome']['no_authorize'];


        $callback_sendMessage = json_decode($core->sendMessage($message, $keyboard, NULL), true);
//        $SQL->UPDATE('users', "message_id = '{$callback_sendMessage['result']['message_id']}'", "id = $user_id");
        $core->deleteMessage($sql_result_user['message_id']);
        $core->deleteMessage($sql_result_user['callback_id']);
        break;

    /* ADMIN ZONE */

    case 'product_add':

        $keyboard = new Keyboard('inline_keyboard', false);
        $keyboard = $keyboard->AUTO_CREATE('admin_product_add', $text_keyboard, $sql_result);

        $core->editMessageText('Придумайте название', $user_id, $sql_result['callback_id'], $keyboard);


        break;
}





//$result = $SQL->link()->query("SELECT brand.description AS 'Brand',
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
//$keyboard = new Keyboard('inline_keyboard', false);
//$keyboard = $keyboard->AUTO_CREATE('test_2', $data['data'], $result);


//$core->sendMessage($text_message['welcome'].$val[1], $user_id, null);
//$core->editMessageText($text_message['welcome'], $user_id, $data['message']['message_id'],$keyboard);