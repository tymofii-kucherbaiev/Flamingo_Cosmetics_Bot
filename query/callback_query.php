<?php
/**
 * @noinspection ALL
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
 * @var $inline_keyboard array
 * @var $message_id string
 * @var $profile_order array
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
            $mysqli->query("CALL PC_update('callback_id = \'{$callback['result']['message_id']}\'', '$user_id', 'users')");
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
            $mysqli->query("CALL PC_update('callback_id = \'{$callback['result']['message_id']}\'', '$user_id', 'users')");
        }
        $core->deleteMessage($mysqli_result_users['service_id']);
        break;

    case 'back_main_search':
        if ($callback_variation == 'sendPhoto') {
            $core->deleteMessage($mysqli_result_users['callback_id']);


            $keyboard->callback_data_action = 'search_product_list';
            $keyboard->callback_data_type = $callback_type;

            $callback = json_decode($core->sendMessage($text_filling['message']['search']['main'], $keyboard->search_main_menu()), TRUE);
            $mysqli->query("CALL PC_update('callback_id = \'{$callback['result']['message_id']}\'', '$user_id', 'users')");
        } else {

            $core->editMessageText($text_filling['message']['search']['main'], $mysqli_result_users['callback_id'], $keyboard->search_main_menu());
        }
        break;

    case 'description':
        $sql_result_local = $mysqli->query("SELECT caption FROM product WHERE vendor_code LIKE $callback_type")->fetch();

        $callback = json_decode($core->sendMessage($sql_result_local['caption'],
            $keyboard->product_description()), TRUE);
        $mysqli->query("CALL PC_update('service_id = \'{$callback['result']['message_id']}\'', '$user_id', 'users')");
        break;

    case 'close':
        if ($callback_type == 'description')
            $core->deleteMessage($mysqli_result_users['service_id']);
        elseif ($callback_type == 'extra')
            $core->deleteMessage($data['message']['message_id']);
        elseif ($callback_type == 'favorite') {
            $core->deleteMessage($data['message']['message_id']);
            $core->deleteMessage($mysqli_result_users['service_id']);
//            $local_user_result = $mysqli->query("SELECT * FROM users_favorite_products WHERE user_id LIKE $user_id")->fetchAll();
//
//            if ($local_user_result) {
//
//                $quality_row = count($local_user_result);
//                if ($quality_row > 5) {
//                    $keyboard->keyboard_type = 'inline_keyboard';
//                    $keyboard->callback_data_action = 'primary';
//                }
//
//                $local_text = "Ваш список желаемого:\n";
//                $local_num = 1;
//
//                foreach ($local_user_result as $value) {
//                    $pr_local = $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE {$value['vendor_code']}")->fetch();
//
//                    $local_text .= "
//—————————————————————————
//<b>№$local_num   /{$pr_local['vendor_code']}</b>    <b>Цена: {$pr_local['price_old']}</b> {$text_filling['currency']}
//<i>{$pr_local['title']}</i>
//—————————————————————————
//";
//                    $local_num++;
//                }
//                if ($quality_row > 5)
//                    $callback = json_decode($core->sendMessage($local_text, $keyboard->profile_favorite()), true);
//                else
//                    $callback = json_decode($core->sendMessage($local_text), true);
//            } else
//                $callback = json_decode($core->sendMessage($text_filling['message']['favorite']['null']), true);
//            $mysqli->query("CALL PC_update('message_id = \'{$callback['result']['message_id']}\'', '$user_id', 'users')");
        } elseif ($callback_type == 'cart') {
            $core->deleteMessage($mysqli_result_users['service_id']);
            $keyboard->mysqli_result =
            $function->mysqli_result =
            $local_user_result =
                $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE $user_id")->fetchAll();
            $mysqli->query("CALL PC_update('order_position = NULL', '$user_id', 'users')");

            $keyboard->keyboard_type = 'inline_keyboard';
            $core->editMessageText($function->profile_list(), $message_id, $keyboard->profile_list());


            if ($profile_order[$user_id]['remember_order'] === true)
                $profile_order[$user_id]['comment'] = 'Отсутствует';
            else {
                $profile_order[$user_id]['first_name'] = NULL;
                $profile_order[$user_id]['last_name'] = NULL;
                $profile_order[$user_id]['phone_number'] = NULL;
                $profile_order[$user_id]['address_delivery'] = NULL;
                $profile_order[$user_id]['address_pickup'] = NULL;
                $profile_order[$user_id]['comment'] = 'Отсутствует';
            }
            file_put_contents('./json/order_general.json', json_encode($profile_order, JSON_UNESCAPED_UNICODE));
        } elseif ($callback_type == 'admin')

            $core->deleteMessage($mysqli_result_users['admin_id']);
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
            $mysqli->query("CALL PC_update('service_id = \'{$callback['result']['message_id']}\'', '$user_id', 'users')");
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

            $local_text .= "\n <b>🛒 Сумма заказа:</b> $local_sum {$text_filling['currency']}";
            if ($local_sum < 1000) {
                $local_text .= "\n <b>📦 Доставка:</b> {$text_filling['delivery_price']} {$text_filling['currency']} (Бесплатная от {$text_filling['delivery_free']} {$text_filling['currency']})";
                $local_sum = $local_sum + $text_filling['delivery_price'];
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

        $core->editMessageText($function->profile_list(true), $message_id, $keyboard->edit_order());
        break;

    case 'remove_product':
        if ($mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE $user_id AND vendor_code LIKE $callback_type")->fetch()['modify_quality'] != 1)
            $mysqli->query("UPDATE users_cart_products SET modify_quality = modify_quality - 1 WHERE user_id LIKE $user_id AND vendor_code LIKE $callback_type");

        $keyboard->mysqli_result =
        $function->mysqli_result =
            $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE $user_id AND is_status LIKE TRUE")->fetchAll();


        $core->editMessageText($function->profile_list(true), $data['message']['message_id'], $keyboard->edit_order());
        break;

    # Блок оформления заказа

    case 'ordering':
        $core->deleteMessage($mysqli_result_users['service_id']);

        if ($profile_order[$user_id]['remember_order'] === true and $callback_variation == 'set_name') {
            $callback_variation = 'set_delivery';
        }

        if ($callback_variation == 'set_edit') {
            $profile_order[$user_id]['first_name'] = NULL;
            $profile_order[$user_id]['last_name'] = NULL;
            $profile_order[$user_id]['phone_number'] = NULL;
            $profile_order[$user_id]['address_delivery'] = NULL;
            $profile_order[$user_id]['address_pickup'] = NULL;
            $profile_order[$user_id]['comment'] = 'Отсутствует';
            $profile_order[$user_id]['remember_order'] = FALSE;

            file_put_contents('./json/order_general.json', json_encode($profile_order, JSON_UNESCAPED_UNICODE));
            $profile_order = json_decode(file_get_contents('./json/order_general.json'), true);
        }


        if ($callback_type) {
            switch ($callback_type) {
                case 'golden_ring':
                    $local_text = 'ТРЦ "Золотое Кольцо"';
                    break;

                case 'donetsk_city':
                    $local_text = 'ТРЦ "Донецк Сити"';
                    break;
            }

            $profile_order[$user_id]['address_pickup'] = $local_text;
            file_put_contents('./json/order_general.json', json_encode($profile_order, JSON_UNESCAPED_UNICODE));
        }

        if ($callback_variation == 'set_confirm' or $callback_variation == 'remember_off' or $callback_variation == 'remember_on') {


            if ($callback_variation == 'remember_on')
                $profile_order[$user_id]['remember_order'] = false;
            elseif ($callback_variation == 'remember_off')
                $profile_order[$user_id]['remember_order'] = true;

            file_put_contents('./json/order_general.json', json_encode($profile_order, JSON_UNESCAPED_UNICODE));


            $profile_order = json_decode(file_get_contents('./json/order_general.json'), true);
            $keyboard->callback_data_type = $profile_order[$user_id]['remember_order'];


            $result_information_product =
                $mysqli->query("SELECT * FROM order_general WHERE user_id LIKE $user_id ORDER BY -id LIMIT 1")->fetch();

            $user_cart_products =
                $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE $user_id")->fetchAll();

            $local_text = "";
            $local_num = 1;
            $local_sum = 0;
            foreach ($user_cart_products as $value) {
                $res_prod = $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE {$value['vendor_code']}")->fetch();

                $local_text .= "<b>№$local_num   /{$value['vendor_code']}</b>  <b>{$value['quality']} шт.</b>  <b>Цена: {$res_prod['price_old']}</b> {$text_filling['currency']}
<i>{$res_prod['title']}</i>
————————————————————————
";
                $local_sum = $local_sum + ($res_prod['price_old'] * $value['quality']);
                $local_num++;
            }

            $local_text .= "\n <b>🛒 Сумма заказа:</b> $local_sum {$text_filling['currency']}";
            if ($local_sum < 1000) {
                $local_text .= "\n <b>📦 Доставка:</b> {$text_filling['delivery_price']} {$text_filling['currency']}";
                $local_sum = $local_sum + $text_filling['delivery_price'];
            } else
                $local_text .= "\n <b>📦 Доставка: 🆓 Бесплатно 🆓</b>";

            $local_text .= "\n <b>💳 К оплате:</b> $local_sum {$text_filling['currency']}";

            $caption = "
————————————————————————
<b>Имя и Фамилия:</b> <i>{$profile_order[$user_id]['first_name']} {$profile_order[$user_id]['last_name']}</i>
<b>Телефон:</b> <i>+{$profile_order[$user_id]['phone_number']}</i>
————————————————————————
<b>Адресс доставки:</b> <i>{$profile_order[$user_id]['address_pickup']}</i>
<b>Комментарий:</b> <i>{$profile_order[$user_id]['comment']}</i>
————————————————————————
<b>Товары:</b>
————————————————————————
$local_text";
        }


        $keyboard->mysqli_result =
            $mysqli->query("CALL PC_update('{$set_table}order_position = \'{$callback_variation}\'', '$user_id', 'users')")->fetch();
        $keyboard->callback_data_variation = $callback_variation;


        $core->editMessageText($text_filling['message']['order'][$callback_variation] . $caption, $message_id, $keyboard->ordering());
        break;

    case 'order_confirm':
        $res = $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE $user_id")->fetchAll();


        $res_us = $mysqli->query("SELECT * FROM order_general WHERE user_id LIKE $user_id ORDER BY -id LIMIT 1")->fetch();

        $local_text = "";
        $local_num = 1;
        $local_sum = 0;
        foreach ($res as $value) {
            $mysqli->query("CALL PC_insert('order_products', '*', '{$res_us['id']}, $user_id, {$value['vendor_code']}, {$value['quality']}')");
            $res_prod = $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE {$value['vendor_code']}")->fetch();

            $local_sum = $local_sum + ($res_prod['price_old'] * $value['quality']);

            $local_text .= "<b>№$local_num   /{$value['vendor_code']}</b>  <b>{$value['quality']} шт.</b>  <b>Цена: {$res_prod['price_old']}</b> {$text_filling['currency']}
<i>{$res_prod['title']}</i>
————————————————————————
";
            $local_num++;
        }


        if ($local_sum >= $text_filling['delivery_free'])
            $is_delivery = 'Бесплатно';
        else
            $is_delivery = $text_filling['delivery_price'];

        $mysqli->query("CALL PC_insert('order_general', 'user_id, profile_first_name, profile_last_name, payment_amount, is_delivery, address_pickup, phone_number, is_comment, is_status, is_active', '$user_id, \'{$profile_order[$user_id]['first_name']}\', \'{$profile_order[$user_id]['last_name']}\', $local_sum, \'$is_delivery\', \'{$profile_order[$user_id]['address_pickup']}\', {$profile_order[$user_id]['phone_number']}, \'{$profile_order[$user_id]['comment']}\', \'new\', 1')");
        $res_us = $mysqli->query("SELECT * FROM order_general WHERE user_id LIKE $user_id ORDER BY -id LIMIT 1")->fetch();


        $callback = json_decode($core->sendMessage($text_filling['message']['order']['complete']), true);
        $mysqli->query("CALL PC_update('message_id = \'{$callback['result']['message_id']}\'', '$user_id', 'users')");

        unset ($is_delivery, $callback);
        foreach ($mysqli->query("SELECT * FROM users WHERE role LIKE 'administrator'")->fetchAll() as $value) {


            if ($res_us['is_delivery'] == 'Бесплатно')
                $is_delivery = "{$res_us['payment_amount']} {$text_filling['currency']}";
            else
                $is_delivery = $res_us['payment_amount'] + $res_us['is_delivery'];

            $caption = "<b>НОВЫЙ ЗАКАЗ!</b>
Заказ №: {$res_us['id']} 
————————————————————————
<b>Имя и Фамилия:</b> <code>{$profile_order[$user_id]['first_name']} {$profile_order[$user_id]['last_name']}</code>
<b>Телефон:</b> <code>+{$profile_order[$user_id]['phone_number']}</code>
————————————————————————
<b>Сумма заказа:</b> <i>{$res_us['payment_amount']} {$text_filling['currency']}</i>
<b>Доставка:</b> <i>{$res_us['is_delivery']}</i>
<b>Общая сумма:</b> <i>$is_delivery</i>
————————————————————————
<b>Адресс доставки:</b> <i>{$profile_order[$user_id]['address_pickup']}</i>
<b>Комментарий:</b> <i>{$profile_order[$user_id]['comment']}</i>

##############################
##############################
————————————————————————
$local_text";

            $core->chat_id = $value['user_id'];
            $core->sendMessage($caption, $keyboard->admin_order_control());
        }

        $core->deleteMessage($mysqli_result_users['message_id']);
        $mysqli->query("DELETE FROM users_cart_products WHERE user_id LIKE $user_id");
        unset($caption);
        break;

    /* Добавление в избранное и корзину */

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

        if ($pr_local['error']) {
            if ($table_name == 'favorite') {
                $mysqli->query("DELETE FROM users_favorite_products WHERE user_id LIKE $user_id AND users_favorite_products.vendor_code LIKE $callback_variation");
            }
            $core->answerCallbackQuery($text_filling['callback'][$callback_action . '_false'], $data['id'], true);
        } else
            $core->answerCallbackQuery($text_filling['callback'][$callback_action . '_true'], $data['id']);
        $core->deleteMessage($mysqli_result_users['service_id']);
        break;

    case 'admin':
        switch ($callback_type) {
            case 'edit_profile':

                break;

            case 'order_list':


                switch ($callback_variation) {
                    case 'new':
                    case 'in_work':
                    case 'completed':
                    case 'cancel':


                        $mysqli_order_general = $mysqli->query("SELECT * FROM order_general WHERE is_status LIKE '$callback_variation' LIMIT 50")->fetchAll();

                        $caption = '';

                        foreach ($mysqli_order_general as $item) {
                            if ($item['is_delivery'] != $text_filling['delivery_price'])
                                $is_delivery = " <b>|</b> 🆓";

                            $caption .= "————————————————————————\n";
                            $caption .= "{$text_filling['icon'][$callback_variation]} <b>|</b> <b>/order_card__{$item['id']}</b> <b>|</b> {$item['payment_amount']} {$text_filling['currency']} <b>|</b> {$item['profile_first_name']}$is_delivery\n";

                            unset($is_delivery);
                        }
                        if (count($mysqli_order_general) != 0)
                            $caption .= "————————————————————————";

                        $core->editMessageText($caption, $message_id, $keyboard->admin_order_list());
                        break;

                    default:
                        $core->editMessageText('hello', $message_id, $keyboard->admin_order_list());
                        break;
                }
                break;
        }
        break;

    default:
        $core->answerCallbackQuery(callback_query_id: $data['id']);
}
