<?php
/**
 * @var $mysqli_result_users mysqli_result
 * @var $mysqli mysqli_result
 * @var $core api
 * @var $function other
 * @var $keyboard keyboard
 * @var $data array
 * @var $user_id integer
 * @var $text_filling array
 * @var $bool_via_bot boolean
 * @var $profile_order array
 */

$core->deleteMessage($data['message_id']);
switch ($data['text']) {
    case $text_filling['command']['start']:
        $callback_local = json_decode($core->sendMessage($mysqli_result_users['first_name'] . $text_filling['message']['welcome'],
            $keyboard->main_menu()), true);
        $core->deleteMessage($mysqli_result_users['main_menu_id']);
        $mysqli->query("CALL PC_update('main_menu_id = \'{$callback_local['result']['message_id']}\', order_position = NULL', 'users', 'user_id', '$user_id')");
        $core->deleteMessage($mysqli_result_users['message_id']);


        if ($profile_order[$user_id]['remember_order'] === false) {
            $profile_order[$user_id] = [
                'first_name' => NULL,
                'last_name' => NULL,
                'phone_number' => NULL,
                'address_delivery' => NULL,
                'address_pickup' => NULL,
                'remember_order' => false,
                'comment' => 'Отсутствует'
            ];
            file_put_contents('./json/order_general.json', json_encode($profile_order, JSON_UNESCAPED_UNICODE));
        }
        break;

    case $text_filling['command']['search']:
    case $text_filling['keyboard']['main']['search']:
        $keyboard->keyboard_type = 'inline_keyboard';
        $keyboard->callback_data_action = 'search_product_list';

        $keyboard->callback_data_type = 'category';
        $callback = json_decode($core->sendMessage($text_filling['message']['search']['callback_category'],
            $keyboard->search_main_product()), true);
        $mysqli->query("CALL PC_update('callback_id = \'{$callback['result']['message_id']}\'', 'users', 'user_id', '$user_id')");
        $core->deleteMessage($mysqli_result_users['message_id']);
        break;

    case $text_filling['keyboard']['main']['cart']:
        $keyboard->mysqli_result =
        $function->mysqli_result =
        $local_user_result =
            $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE $user_id")->fetchAll();

        if ($local_user_result) {
            $keyboard->keyboard_type = 'inline_keyboard';
            $callback = json_decode($core->sendMessage($function->profile_list(), $keyboard->profile_list()), true);
        } else
            $callback = json_decode($core->sendMessage($text_filling['message']['cart']['null']), true);
        $core->deleteMessage($mysqli_result_users['message_id']);
        break;

    case $text_filling['command']['help']:
    case $text_filling['keyboard']['main']['help']:
        # Переработать
        $callback = json_decode($core->sendMessage($text_filling['message']['help']), true);
        $core->deleteMessage($mysqli_result_users['message_id']);
        break;

    case $text_filling['keyboard']['main']['favorite']:
        $mysqli_favorite = $mysqli->query("SELECT * FROM users_favorite_products WHERE user_id LIKE $user_id")->fetchAll();

        if ($mysqli_favorite) {


            $keyboard->keyboard_type = 'inline_keyboard';
            if (count($mysqli_favorite) > 10) {
                $keyboard->callback_data_type = 'next';

            }

            $i = 0;
            $caption = NULL;
            foreach ($mysqli_favorite as $item) {
                if ($i >= 10)
                    break;
                else
                    $i++;

                $info_product = $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE {$item['vendor_code']}")->fetch();


                $caption .= "————————————————————————\n";
                $caption .= "<b>№$i   /{$info_product['vendor_code']}   Цена: {$info_product['price_old']} {$text_filling['currency']}</b>
<i>{$info_product['title']}</i>\n";

            }
            $caption .= "————————————————————————";
            $callback = json_decode($core->sendMessage($caption, $keyboard->profile_favorite()), true);
        } else
            $callback = json_decode($core->sendMessage($text_filling['message']['favorite']['null']), true);
        $core->deleteMessage($mysqli_result_users['message_id']);
        break;

    case $text_filling['keyboard']['main']['admin']:
        if ($mysqli_result_users['role'] == 'administrator') {
            $core->deleteMessage($mysqli_result_users['admin_id']);
            $keyboard->keyboard_type = 'inline_keyboard';

            $callback_local = json_decode($core->sendMessage($text_filling['message']['admin_main_menu'], $keyboard->admin_main_menu()), true);
            $mysqli->query("CALL PC_update('admin_id = \'{$callback_local['result']['message_id']}\'', 'users', 'user_id', '$user_id')");
        }
        break;

    case $text_filling['keyboard']['main']['history_order']:
        $mysqli_order_general = $mysqli->query("SELECT * FROM order_general WHERE user_id LIKE $user_id ORDER BY id DESC")->fetchAll();

        $caption = "————————————————————————
| {$text_filling['icon']['new']} | Новый
————————————————————————
| {$text_filling['icon']['in_work']} | В работе
————————————————————————
| {$text_filling['icon']['completed']} | Завершённый
————————————————————————
| {$text_filling['icon']['cancel']} | Отмененный
————————————————————————\n\n";
        $i = 0;
        foreach ($mysqli_order_general as $item) {
            if ($i >= 10)
                break;
            else
                $i++;

            $is_status = match ($item['is_status']) {
                'new' => $text_filling['icon']['new'],
                'in_work' => $text_filling['icon']['in_work'],
                'completed' => $text_filling['icon']['completed'],
                default => $text_filling['icon']['cancel'],
            };

            $caption .= "————————————————————————\n";
            $caption .= "<b>|</b> $is_status <b>|</b> <b>/my_order__{$item['id']}</b> <b>|</b> {$item['date_time']} <b>|</b> {$item['payment_amount']} {$text_filling['currency']}\n";
        }

        if (count($mysqli_order_general) != 0)
            $caption .= "————————————————————————";
        $keyboard->keyboard_type = 'inline_keyboard';
        if (count($mysqli_order_general) > 10) {
            $keyboard->callback_data_type = 'next';

        }
        $callback = json_decode($core->sendMessage($caption, $keyboard->profile_history_order()), true);

        $core->deleteMessage($mysqli_result_users['message_id']);
        break;

    default:
        switch ($mysqli_result_users['order_position']) {
            case 'set_name':
            case 'set_phone':
            case 'set_delivery':
            case 'set_comment':
            case 'set_edit':
                $local_variation = NULL;
                $complete = FALSE;


                switch ($mysqli_result_users['order_position']) {
                    case 'set_name':
                    case 'set_edit':
                        $explode_full_name = explode(' ', $data['text']);
                        if (!$explode_full_name[1]) {
                            $local_callback = json_decode($core->sendMessage($text_filling['message']['error_order']['set_name']), TRUE);
                            $mysqli->query("CALL PC_update('service_id = \'{$local_callback['result']['message_id']}\'', 'users', 'user_id', '$user_id')");
                        } else {

                            $profile_order[$user_id]['first_name'] = $explode_full_name[0];
                            $profile_order[$user_id]['last_name'] = $explode_full_name[1];
                            file_put_contents('./json/order_general.json', json_encode($profile_order, JSON_UNESCAPED_UNICODE));

                            $local_variation = 'set_phone';
                        }

                        break;

                    case 'set_phone':
                        if (preg_match('/^[0-9]+$/i', $data['text']) == 0 or iconv_strlen($data['text']) != 11 and iconv_strlen($data['text']) != 12) {
                            $local_callback = json_decode($core->sendMessage($text_filling['message']['error_order']['set_phone']), TRUE);
                            $mysqli->query("CALL PC_update('service_id = \'{$local_callback['result']['message_id']}\'', 'users', 'user_id', '$user_id')");
                        } else {
                            $profile_order[$user_id]['phone_number'] = $data['text'];
                            file_put_contents('./json/order_general.json', json_encode($profile_order, JSON_UNESCAPED_UNICODE));

                            $local_variation = 'set_delivery';
                        }
                        break;

                    case 'set_delivery':
                        $local_callback = json_decode($core->sendMessage($text_filling['message']['error_order']['set_delivery']), TRUE);
                        $mysqli->query("CALL PC_update('service_id = \'{$local_callback['result']['message_id']}\'', 'users', 'user_id', '$user_id')");
                        break;

                    case 'set_comment':
                        $profile_order[$user_id]['comment'] = $data['text'];
                        file_put_contents('./json/order_general.json', json_encode($profile_order, JSON_UNESCAPED_UNICODE));

                        $local_variation = 'set_confirm';
                        break;

                }
                if ($local_variation) {

                    $keyboard->mysqli_result = $mysqli->query("CALL PC_update('order_position = \'$local_variation\'', 'users', 'user_id', '$user_id')")->fetch();
                    $keyboard->callback_data_variation = $local_variation;
                    $keyboard->keyboard_type = 'inline_keyboard';
                    $keyboard->callback_data_type = $profile_order[$user_id]['remember_order'];

                    $local_sum = 0;
                    if ($local_variation == 'set_confirm') {
                        $result_information_product =
                            $mysqli->query("SELECT * FROM order_general WHERE user_id LIKE $user_id ORDER BY -id LIMIT 1")->fetch();

                        $user_cart_products =
                            $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE $user_id")->fetchAll();

                        $local_text = "";
                        $local_num = 1;
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
                        if ($local_sum <= $text_filling['delivery_price']) {
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

                    $core->editMessageText($text_filling['message']['order'][$local_variation] . $caption, $mysqli_result_users['message_id'], $keyboard->ordering());
                }
                break;

            default:
                if (stristr($data['text'], 'order_card') == substr($data['text'], 1)) {


                    $order_id = explode('__', $data['text'])[1];
                    $mysqli_order_general = $mysqli->query("SELECT * FROM order_general WHERE id LIKE $order_id")->fetch();
                    $mysqli_order_products = $mysqli->query("SELECT * FROM order_products WHERE order_id LIKE $order_id")->fetchAll();

                    $function->user_id = $mysqli_order_general['user_id'];

                    $number = 1;
                    $caption_product = NULL;

                    foreach ($mysqli_order_products as $value) {
                        $mysqli_product = $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE {$value['vendor_code']}")->fetch();
                        $caption_product .= $function->product_card($mysqli_product, $number, $value['quality']);
                        $number++;
                    }

                    $caption_message = $function->order_confirm($mysqli_order_general) . $caption_product;

                    $keyboard->keyboard_type = 'inline_keyboard';
                    $keyboard->callback_data_variation = $order_id;
                    $keyboard->callback_data_type = $mysqli_order_general['is_status'];
                    if ($mysqli_order_general['is_status'] != 'completed' and $mysqli_order_general['is_status'] != 'cancel')
                        $local_keyboard = $keyboard->admin_order_control();

                    $callback = json_decode($core->sendMessage($caption_message, $local_keyboard), true);
                    $mysqli->query("CALL PC_update('admin_service_id = \'{$callback['result']['message_id']}\'', 'users', 'user_id', '$user_id')");
                    $core->deleteMessage($mysqli_result_users['admin_service_id']);
                } elseif (stristr($data['text'], 'my_order') == substr($data['text'], 1)) {
                    $order_id = explode('__', $data['text'])[1];

                    $mysqli_order_general = $mysqli->query("SELECT * FROM order_general WHERE id LIKE $order_id")->fetch();
                    $mysqli_order_products = $mysqli->query("SELECT * FROM order_products WHERE order_id LIKE $order_id")->fetchAll();
                    $number = 1;
                    $caption_product = NULL;

                    foreach ($mysqli_order_products as $value) {
                        $mysqli_product = $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE {$value['vendor_code']}")->fetch();
                        $caption_product .= $function->product_card($mysqli_product, $number, $value['quality']);
                        $number++;
                    }


                    $caption_message = $function->order_history($mysqli_order_general) . $caption_product;

                    $keyboard->keyboard_type = 'inline_keyboard';
                    $keyboard->callback_data_type = 'extra';

                    $callback = json_decode($core->sendMessage($caption_message, $keyboard->close()), true);

                    $mysqli->query("CALL PC_update('admin_service_id = \'{$callback['result']['message_id']}\'', 'users', 'user_id', '$user_id')");
                    $core->deleteMessage($mysqli_result_users['admin_service_id']);

                } elseif (iconv_strlen($data['text']) == 14) {
                    $data['text'] = substr($data['text'], 1);
                    $keyboard->keyboard_type = 'inline_keyboard';
                    if ($bool_via_bot === TRUE) {
                        $keyboard->mysqli_result = $local_mysqli_result =
                            $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE {$data['text']}")->fetch();

                        $callback = json_decode($core->sendPhoto($local_mysqli_result['title'],
                            $local_mysqli_result['image_id'],
                            $keyboard->other_variation_product()), true);

                    } else {
                        $keyboard->callback_data_action = $data['text'];
                        $keyboard->callback_data_type = 'favorite';

                        $keyboard->mysqli_result = $mysqli_product_card =
                            $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE {$data['text']}")->fetch();

                        if ($mysqli_result_users['role'] == 'administrator') {
                            $brand = $mysqli->query("SELECT * FROM brand WHERE id LIKE {$mysqli_product_card['brand_id']}")->fetch();
                            $category = $mysqli->query("SELECT * FROM category WHERE id LIKE {$mysqli_product_card['category_id']}")->fetch();

                            $caption = "<b>Название:</b> <u><b>" . $mysqli_product_card['title'] . "</b></u>\n";
                            $caption .= "<b>Категория:</b> <u><b>" . $category['description'] . "</b></u>\n";
                            $caption .= "<b>Бренд:</b> <u><b>" . $brand['description'] . "</b></u>\n";
                            $caption .= "—————————————————————————\n";
                            $caption .= "<b>Описание:</b> <i>" . $mysqli_product_card['caption'] . "</i>\n";


                        } else
                            $caption = $mysqli_product_card['title'];


                        $callback = json_decode($core->sendPhoto($caption, $mysqli_product_card['image_id'], $keyboard->product_card()), true);
                        $core->deleteMessage($mysqli_result_users['extra_id']);
                    }
                    $mysqli->query("CALL PC_update('extra_id = \'{$callback['result']['message_id']}\'', 'users', 'user_id', '$user_id')");
                    unset ($callback);
                } else {
                    $core->deleteMessage($data['message_id']);
                }
        }
        break;
}

if ($callback)
    $mysqli->query("CALL PC_update('message_id = \'{$callback['result']['message_id']}\', order_position = NULL', 'users', 'user_id', '$user_id')");

if ($bool_via_bot === FALSE)
    $core->deleteMessage($mysqli_result_users['callback_id']);

$core->deleteMessage($mysqli_result_users['service_id']);
$core->deleteMessage($mysqli_result_users['extra_id']);
