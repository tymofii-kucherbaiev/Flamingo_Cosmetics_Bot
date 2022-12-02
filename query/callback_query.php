<?php /** @noinspection ALL */
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

if ($callback_action != 'product_favorite' and $callback_action != 'product_cart')
    $core->answerCallbackQuery(callback_query_id: $data['id']);
$keyboard->keyboard_type = 'inline_keyboard';
switch ($callback_action) {
    case 'search_main_menu':
        # Обрабатывает нажатие одну из 2-х кнопок
        # По брендам или По категориям
        # Выдает список брендов или категорий соответсвенно


        $keyboard->callback_data_action = 'search_product_list';

        if ($callback_type == 'card') {
            $core->deleteMessage($mysqli_result_users['callback_id']);
            $keyboard->callback_data_type = $callback_variation;
            $callback = json_decode($core->sendMessage($text_filling['message']['search']['callback_' . $callback_variation],
                $keyboard->search_main_product()), true);
            $mysqli->query("CALL PC_update('callback_id', '{$callback['result']['message_id']}', '$user_id', 'users')");
        } else {
            $keyboard->callback_data_type = $callback_type;
            $core->editMessageText($text_filling['message']['search']['callback_' . $callback_type],
                $mysqli_result_users['callback_id'], $keyboard->search_main_product());
        }
        break;

    case 'search_product_list':
        # Обрабатывает нажатие на бренд или категорию в списке

        $keyboard->callback_data_action = 'search_product_list';
        $keyboard->callback_data_variation = 'category';

        if ($callback_type == 'back' || $callback_type == 'next') {

            $scroll_value = $data['message']['reply_markup']['inline_keyboard'][2][1]['callback_data'];

            $scroll_callback_type = explode(':', explode('|', $scroll_value)[1])[1];
            $scroll_callback_variation = explode(':', explode('|', $scroll_value)[2])[1];

            $product_value = $data['message']['reply_markup']['inline_keyboard'][0][1]['callback_data'];

            $product_callback_type = explode(':', explode('|', $product_value)[1])[1];
            $product_callback_variation = explode(':', explode('|', $product_value)[2])[1];

            if ($callback_type == 'next') {
                if ($scroll_callback_type == $scroll_callback_variation)
                    $scroll = $keyboard->callback_data_type = 1;
                else
                    $scroll = $keyboard->callback_data_type = $scroll_callback_type + 1;
            }

            if ($callback_type == 'back') {
                if ($scroll_callback_type == 1)
                    $scroll = $keyboard->callback_data_type = $scroll_callback_variation;
                else
                    $scroll = $keyboard->callback_data_type = $scroll_callback_type - 1;
            }

            $keyboard->mysqli_result = $mysqli_product_card =
                $mysqli->query("CALL PC_product_card('$product_callback_variation', $product_callback_type)")->fetchAll();

            $core->editMessageMedia($mysqli_result_users['callback_id'], $mysqli_product_card[$scroll - 1]['title'],
                $mysqli_product_card[$scroll - 1]['image_id'], $keyboard->search_product_list());
        } else {


            # Удаление сообщения со списком брендов или категорий
            $core->deleteMessage(message_id: $mysqli_result_users['callback_id']);


            $keyboard->callback_data_action = 'search_product_list';
            $keyboard->callback_data_variation = $callback_variation; #category or brand
            $keyboard->callback_data_type = 1; #Текущая позиция продукта (по умолчанию 1)

            $keyboard->mysqli_result = $mysqli_product_card =
                $mysqli->query("CALL PC_product_card('$callback_variation', $callback_type)")->fetchAll();

            $callback = json_decode($core->sendPhoto($mysqli_product_card[0]['title'],
                $mysqli_product_card[0]['image_id'], $keyboard->search_product_list()), TRUE);
            $mysqli->query("CALL PC_update('callback_id', '{$callback['result']['message_id']}', '$user_id', 'users')");
        }
        $core->deleteMessage($mysqli_result_users['service_id']);
        break;

    case 'back_main_search':
        if ($callback_variation == 'sendPhoto') {
            $core->deleteMessage($mysqli_result_users['callback_id']);


            $keyboard->callback_data_action = 'search_product_list';
            $keyboard->callback_data_type = $callback_type;

            $callback = json_decode($core->sendMessage($text_filling['message']['search']['main'], $keyboard->search_main_menu()), TRUE);
            $mysqli->query("CALL PC_update('callback_id', '{$callback['result']['message_id']}', '$user_id', 'users')");
        } else {

            $core->editMessageText($text_filling['message']['search']['main'], $mysqli_result_users['callback_id'], $keyboard->search_main_menu());
        }
        break;

    case 'description':
        $sql_result_local = $mysqli->query("SELECT caption FROM product WHERE vendor_code LIKE $callback_type")->fetch();

        $callback = json_decode($core->sendMessage($sql_result_local['caption'],
            $keyboard->product_description()), TRUE);
        $mysqli->query("CALL PC_update('service_id', '{$callback['result']['message_id']}', '$user_id', 'users')");
        break;

    case 'close':
        if ($callback_type == 'description')
            $core->deleteMessage($mysqli_result_users['service_id']);
        elseif ($callback_type == 'extra')
            $core->deleteMessage($data['message']['message_id']);
        else
            $core->deleteMessage($mysqli_result_users['callback_id']);
        break;


    case 'product_favorite':
    case 'product_cart':

        if ($callback_action == 'product_cart')
            $data_local = "$user_id, $callback_variation, 1";
        else
            $data_local = "$user_id, $callback_variation";

    $pr_local = $mysqli->query("CALL PC_insert('users_{$callback_type}_products', '*', '$data_local')")->fetch();

        if ($pr_local['error'])
            $core->answerCallbackQuery($text_filling['callback'][$callback_action . '_false'], $data['id'], true);
        else
            $core->answerCallbackQuery($text_filling['callback'][$callback_action . '_true'], $data['id']);
        break;
}
