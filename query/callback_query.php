<?php /** @noinspection ALL */
/**
 * @var $mysqli mysqli_result
 * @var $mysqli_result_users mysqli_result
 * @var $core api
 * @var $function other
 * @var $keyboard keyboard
 * @var $text_filling array
 * @var $callback_action string
 * @var $callback_type string
 * @var $callback_variation string
 * @var $user_id string
 * @var $data array
 */

if ($callback_action != 'product_favorite' and $callback_action != 'product_cart' and $callback_action != 'product_count')
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

    case 'product_count':
        if ($mysqli->query("SELECT * FROM users_cart_products WHERE vendor_code LIKE $callback_variation AND user_id LIKE $user_id")->rowCount() == 1)
            $core->answerCallbackQuery($text_filling['callback']['product_cart_false'], $data['id'], true);
        else {
            $core->answerCallbackQuery(callback_query_id: $data['id']);
            $keyboard->callback_data_type = $callback_variation;
            $callback = json_decode($core->sendMessage('Выберите количество:', $keyboard->count_product_cart()), true);
            $mysqli->query("CALL PC_update('service_id', '{$callback['result']['message_id']}', '$user_id', 'users')");
        }
        break;

    case 'edit_cart':
        $keyboard->mysqli_result =
        $function->mysqli_result =
            $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE $user_id")->fetchAll();

        $core->editMessageText($function->profile_list(), $data['message']['message_id'], $keyboard->edit_order());
        break;

    case 'back_cart':

        if ($callback_type == 'cancel') {
            $local_user_result = $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE 
                                        $user_id AND is_status LIKE FALSE")->fetchAll();
            foreach ($local_user_result as $item) {
                $mysqli->query("UPDATE users_cart_products SET is_status = TRUE WHERE user_id LIKE $user_id AND vendor_code LIKE {$item['vendor_code']}");
            }

            $local_user_result = $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE 
                                        $user_id AND modify_quality NOT LIKE quality")->fetchAll();
            foreach ($local_user_result as $item) {
                $mysqli->query("UPDATE users_cart_products SET modify_quality = quality WHERE user_id LIKE $user_id AND vendor_code LIKE {$item['vendor_code']}");
            }

        } elseif ($callback_type == 'apply') {
            $local_user_result = $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE 
                                        $user_id AND is_status LIKE FALSE")->fetchAll();
            foreach ($local_user_result as $item) {
                $mysqli->query("DELETE FROM users_cart_products WHERE user_id LIKE $user_id AND vendor_code LIKE {$item['vendor_code']}");
            }

            $local_user_result = $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE 
                                        $user_id AND modify_quality NOT LIKE quality")->fetchAll();
            foreach ($local_user_result as $item) {
                $mysqli->query("UPDATE users_cart_products SET quality = modify_quality WHERE user_id LIKE $user_id AND vendor_code LIKE {$item['vendor_code']}");
            }

        }

        if ($mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE $user_id AND is_status LIKE TRUE")->rowCount() == 0) {
            $core->deleteMessage($mysqli_result_users['message_id']);
        } else {
            $local_user_result = $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE $user_id")->fetchAll();
            $keyboard->keyboard_type = 'inline_keyboard';
            $local_text = "Ваша корзина:\n";
            $local_sum = 0;
            $local_num = 1;

            foreach ($local_user_result as $value) {
                $pr_local = $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE {$value['vendor_code']}")->fetch();
                $local_sum = $local_sum + ($pr_local['price_old'] * $value['quality']);

                $local_text .= "
—————————————————————————
<b>№$local_num   /{$pr_local['vendor_code']}</b>  <b>{$value['quality']} шт.</b>  <b>Цена: {$pr_local['price_old']}</b> {$text_filling['currency']}
<i>{$pr_local['title']}</i>
—————————————————————————
";
                $local_num++;
            }

            if ($local_sum < 1000) {
                $local_text .= "\n <b>🛒 Сумма заказа:</b> $local_sum {$text_filling['currency']}";
                $local_text .= "\n <b>📦 Доставка:</b> 100 {$text_filling['currency']} (Беслпатная от 1000 {$text_filling['currency']})";
                $local_sum = $local_sum + 100;
            } else
                $local_text .= "\n <b>📦 Доставка: 🆓 Бесплатно 🆓</b>";

            $local_text .= "\n <b>💳 К оплате:</b> $local_sum {$text_filling['currency']}";
            $core->editMessageText($local_text, $data['message']['message_id'], $keyboard->profile_list());
        }
        break;

    case 'delete_product':
        $mysqli->query("UPDATE users_cart_products SET is_status = FALSE WHERE user_id LIKE $user_id AND vendor_code LIKE $callback_type");

        $keyboard->mysqli_result =
        $function->mysqli_result =
            $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE $user_id AND is_status LIKE TRUE")->fetchAll();

        $core->editMessageText($function->profile_list(), $data['message']['message_id'], $keyboard->edit_order());
        break;

    case 'add_product':
        $mysqli->query("UPDATE users_cart_products SET modify_quality = modify_quality + 1 WHERE user_id LIKE $user_id AND vendor_code LIKE $callback_type");
        $keyboard->mysqli_result =
        $function->mysqli_result =
            $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE $user_id AND is_status LIKE TRUE")->fetchAll();

        $core->editMessageText($function->profile_list(true), $data['message']['message_id'], $keyboard->edit_order());
        break;

    case 'remove_product':
        $mysqli->query("UPDATE users_cart_products SET modify_quality = modify_quality - 1 WHERE user_id LIKE $user_id AND vendor_code LIKE $callback_type");

        $keyboard->mysqli_result =
        $function->mysqli_result =
            $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE $user_id AND is_status LIKE TRUE")->fetchAll();


        $core->editMessageText($function->profile_list(true), $data['message']['message_id'], $keyboard->edit_order());
        break;

    case 'ordering':
        
        break;

    case 'product_favorite':
    case 'product_cart':

        if ($callback_action == 'product_cart') {
            $data_local = "$user_id, $callback_type, $callback_variation, TRUE, $callback_variation";
            $table_name = 'cart';
        } else {
            $table_name = 'favorite';
            $data_local = "$user_id, $callback_variation";
        }

        $pr_local = $mysqli->query("CALL PC_insert('users_{$table_name}_products', '*', '$data_local')")->fetch();

        if ($pr_local['error'])
            $core->answerCallbackQuery($text_filling['callback'][$callback_action . '_false'], $data['id'], true);
        else
            $core->answerCallbackQuery($text_filling['callback'][$callback_action . '_true'], $data['id']);
        $core->deleteMessage($mysqli_result_users['service_id']);
        break;
}
