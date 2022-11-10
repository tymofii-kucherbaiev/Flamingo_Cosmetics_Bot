<?php
/**
 * @var $mysqli mysqli_result
 * @var $mysqli_result_users mysqli_result
 * @var $core API
 * @var $keyboard keyboard
 * @var $text_filling array
 * @var $callback_action string
 * @var $callback_type string
 * @var $callback_variation string
 * @var $user_id string
 * @var $data array
 */

switch ($callback_action) {
    case 'search_main_menu':
        /* Обрабатывает нажатие одну из 2-х кнопок
         * # По брендам # или # По категориям #
         * */

        $keyboard->keyboard_type = 'inline_keyboard';
        $keyboard->callback_data_action = 'search_product_list';
        $keyboard->callback_data_type = $callback_type;

        $core->editMessageText($text_filling['message']['search']['callback_' . $callback_type],
            $mysqli_result_users['callback_id'], $keyboard->search_main_product());

        break;

    case 'search_product_list':
        /* Обрабатывает нажатие на бренд или категорию
         *
         * */

        $core->deleteMessage($mysqli_result_users['callback_id']);

        $core->answerCallbackQuery(callback_query_id: $data['id']);

        $keyboard->keyboard_type = 'inline_keyboard';
        $keyboard->callback_data_type = $callback_type;

        $keyboard->mysqli_result = $mysqli_product_result = $mysqli->query("SELECT * FROM 
             (SELECT COUNT(*) AS count
              FROM (SELECT * FROM product WHERE {$callback_variation}_id LIKE $callback_type GROUP BY group_id) as count) AS count,
     product WHERE {$callback_variation}_id LIKE $callback_type GROUP BY group_id")->fetchall();

        $callback = json_decode($core->sendPhoto($mysqli_product_result[0]['title'],
            $mysqli_product_result[0]['image_id'], $keyboard->search_product_list()), TRUE);
        $mysqli->query("CALL PC_update_user('callback_id', '{$callback['result']['message_id']}', '$user_id')");
        break;

    case 'back_main_search':
        if ($callback_variation == 'sendPhoto') {
            $core->deleteMessage($mysqli_result_users['callback_id']);

            $keyboard->keyboard_type = 'inline_keyboard';
            $keyboard->callback_data_action = 'search_product_list';
            $keyboard->callback_data_type = $callback_type;

            $callback = json_decode($core->sendMessage($text_filling['message']['search']['main'], $keyboard->search_main_menu()), TRUE);
            $mysqli->query("CALL PC_update_user('callback_id', '{$callback['result']['message_id']}', '$user_id')");
        } else {
            $keyboard->keyboard_type = 'inline_keyboard';
            $core->editMessageText($text_filling['message']['search']['main'], $mysqli_result_users['callback_id'], $keyboard->search_main_menu());
        }
        break;

    case 'close':
        $core->deleteMessage($mysqli_result_users['callback_id']);
        $core->deleteMessage($mysqli_result_users['message_id']);

        if ($mysqli_result_users['phone_number']) $message = $mysqli_result_users['first_name'] . $text_filling['message']['welcome'];
        else $message = $text_filling['message']['new_user'];

        $callback = json_decode($core->sendMessage($message, $keyboard->main_menu()), true);

        $mysqli->query("CALL PC_update_user('message_id', '{$callback['result']['message_id']}', '$user_id')");
        break;
}
