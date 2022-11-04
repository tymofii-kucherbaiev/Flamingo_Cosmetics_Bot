<?php
/**
 * @var $mysqli mysqli_result
 * @var $mysqli_result_users mysqli_result
 * @var $core API
 * @var $keyboard keyboard
 * @var $text_filling array
 * @var $callback_action string
 * @var $callback_type string
 * @var $user_id string
 */

switch ($callback_action) {
    case 'search_main_menu':
        $keyboard->keyboard_type = 'inline_keyboard';
        $keyboard->callback_data_action = 'search_product_list';
        $keyboard->callback_data_type = $callback_type;

        $core->editMessageText($text_filling['keyboard']['search']['callback_'.$callback_type],
            $mysqli_result_users['callback_id'], $keyboard->search_main_product());

        break;

    case 'search_product_list':

        break;

    case 'back_main_search':
        $keyboard->keyboard_type = 'inline_keyboard';
        $core->editMessageText($text_filling['message']['search'], $mysqli_result_users['callback_id'], $keyboard->search_main_menu());
        break;

    case 'close':
        $core->deleteMessage($mysqli_result_users['callback_id']);
        break;
}


//$keyboard = new keyboard('keyboard', false, $text_filling, $mysqli_result_users);
//$keyboard = $keyboard->create('main_menu', NULL, NULL);
//
////$core->deleteMessage($mysqli_result_users['message_id']);
//file_put_contents('error.json', $core->editMessageText('as', $mysqli_result_users['message_id'], $keyboard));




//switch ($action) {
//    case 'close':
//        $core->deleteMessage($data['message_id']);
//        $keyboard = new keyboard('keyboard', false);
//        $keyboard = $keyboard->auto_create('main_menu', $text_keyboard, $sql_result_user, NULL, NULL);
//
//        if ($sql_result_user['phone_number'])
//            $message = $sql_result_user['first_name'] . $text_message['welcome']['general'];
//        else
//            $message = $text_message['welcome']['no_authorize'];
//
//
//        $callback_sendMessage = json_decode($core->sendMessage($message, $keyboard, NULL), true);
////        $SQL->UPDATE('users', "message_id = '{$callback_sendMessage['result']['message_id']}'", "id = $user_id");
//        $core->deleteMessage($sql_result_user['message_id']);
//        $core->deleteMessage($sql_result_user['callback_id']);
//        break;
//
//    /* ADMIN ZONE */
//
//    case 'product_add':
//
//        $keyboard = new keyboard('inline_keyboard', false);
//        $keyboard = $keyboard->AUTO_CREATE('admin_product_add', $text_keyboard, $sql_result);
//
//        $core->editMessageText('Придумайте название', $user_id, $sql_result['callback_id'], $keyboard);
//
//
//        break;
//
//    default:
//
//        if ($action == 'description')
//            $vendor_code = $type;
//        else
//            $vendor_code = $type;
//
//        $res = $mysqli->query("call PC_product_list($vendor_code)")->fetchAll();
//        $mysqli_count = $mysqli->query("call PC_product_list($vendor_code)")->rowCount() - 1;
//        $mysqli_result = $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE $vendor_code")->fetch();
//
//        $key = NULL;
//
//        foreach ($res as $val_key => $value) {
//            if ($value['vendor_code'] == $vendor_code)
//                $key = $val_key;
//        }
//
//        if ($mysqli_count == $key)
//            $next = 0;
//        else
//            $next = $key + 1;
//
//        if ($key == 0)
//            $back = $mysqli_count - 1;
//        else
//            $back = $key - 1;
//
//        $vc_next = $res[$next]['vendor_code'];
//        $vc_back = $res[$back]['vendor_code'];
//
//
//        $product_card = [
//            "count" => $mysqli_count,
//            "type_prev" => $res[$next]['vendor_code'],
//            "type_next" => $res[$back]['vendor_code']
//        ];
//
//############################################################################################################################
//
//
//        $keyboard = new keyboard('inline_keyboard', false);
//
//        $keyboard = $keyboard->auto_create('product_card', $text_filling, $mysqli_result, null, $product_card);
//
//
////        $core->sendPhoto($mysqli_result['title'], $mysqli_result['image_id'], $keyboard);
//        $core->editMessageMedia($mysqli_result['title'], $data['message']['message_id'],  $mysqli_result['image_id'], $keyboard);
////        $core->editMessageText($data['message']['message_id'], $data['photo'][2]['file_id'], $keyboard);
//        break;
//}


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
//$keyboard = new keyboard('inline_keyboard', false);
//$keyboard = $keyboard->AUTO_CREATE('test_2', $data['data'], $result);


//$core->sendMessage($text_message['welcome'].$val[1], $user_id, null);
//$core->editMessageText($text_message['welcome'], $user_id, $data['message']['message_id'],$keyboard);