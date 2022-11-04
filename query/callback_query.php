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
        $core->sendMessage($callback_type . $callback_action);
        break;

    case 'back_main_search':
        $keyboard->keyboard_type = 'inline_keyboard';
        $core->editMessageText($text_filling['message']['search'], $mysqli_result_users['callback_id'], $keyboard->search_main_menu());
        break;

    case 'close':
        $core->deleteMessage($mysqli_result_users['callback_id']);
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
//$keyboard = new keyboard('inline_keyboard', false);
//$keyboard = $keyboard->AUTO_CREATE('test_2', $data['data'], $result);
