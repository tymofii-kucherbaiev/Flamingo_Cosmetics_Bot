<?php
/**
 * @var $mysqli_result_users mysqli_result
 * @var $mysqli mysqli_result
 * @var $core API
 * @var $keyboard keyboard
 * @var $data
 * @var $user_id integer
 * @var $text_filling array
 * @var $bool_via_bot boolean
 */

$core->deleteMessage($data['message_id']);
switch ($data['text']) {
    case $text_filling['command']['start']:
        $callback_local = json_decode($core->sendMessage($mysqli_result_users['first_name'] . $text_filling['message']['welcome'],
            $keyboard->main_menu()), true);
        $core->deleteMessage($mysqli_result_users['main_menu_id']);
        $mysqli->query("CALL PC_update('main_menu_id', '{$callback_local['result']['message_id']}', '$user_id', 'users')");
        break;

    case $text_filling['command']['search']:
    case $text_filling['keyboard']['main']['search']:
        $keyboard->keyboard_type = 'inline_keyboard';
        $keyboard->callback_data_action = 'search_product_list';

        $keyboard->callback_data_type = 'category';
        $callback = json_decode($core->sendMessage($text_filling['message']['search']['callback_category'],
            $keyboard->search_main_product()), true);
        $mysqli->query("CALL PC_update('callback_id', '{$callback['result']['message_id']}', '$user_id', 'users')");

//    $keyboard->keyboard_type = 'inline_keyboard';
//
//    $callback = json_decode($core->sendMessage($text_filling['message']['search']['main'],
//        $keyboard->search_main_product()), true);

        break;

    case $text_filling['keyboard']['main']['cart']:
        $ur_local = $mysqli->query("SELECT * FROM users_cart_products WHERE user_id LIKE $user_id")->fetchAll();

        if ($ur_local) {
            $keyboard->keyboard_type = 'inline_keyboard';
            $local_text = "Ваша корзина:\n";
            $local_sum = 0;
            $local_num = 1;

            foreach ($ur_local as $value) {
                $pr_local = $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE {$value['vendor_code']}")->fetch();
                $local_sum = $local_sum + $pr_local['price_old'];

                $local_text .= "
—————————————————————————
<b>№$local_num   /{$pr_local['vendor_code']}</b>    <b>Цена: {$pr_local['price_old']}</b> {$text_filling['currency']}
<i>{$pr_local['title']}</i>
—————————————————————————
";
                $local_num++;
            }

            $local_text .= "\n <b>Общая сумма заказа:</b> $local_sum {$text_filling['currency']}";
            $callback = json_decode($core->sendMessage($local_text, $keyboard->profile_cart()), true);
        } else
            $callback = json_decode($core->sendMessage($text_filling['message']['cart']['null']), true);
        break;

    case $text_filling['command']['help']:
    case $text_filling['keyboard']['main']['help']:
        # Переработать
        $callback = json_decode($core->sendMessage($text_filling['message']['help']), true);
        break;

    case $text_filling['keyboard']['main']['favorite']:
        $ur_local = $mysqli->query("SELECT * FROM users_favorite_products WHERE user_id LIKE $user_id")->fetchAll();

        if ($ur_local) {

            $quality_row = count($ur_local);
            if ($quality_row > 5) {
                $keyboard->keyboard_type = 'inline_keyboard';
                $keyboard->callback_data_action = 'primary';
            }

            $local_text = "Ваш список желаемого:\n";
            $local_num = 1;

            foreach ($ur_local as $value) {
                $pr_local = $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE {$value['vendor_code']}")->fetch();

                $local_text .= "
—————————————————————————
<b>№$local_num   /{$pr_local['vendor_code']}</b>    <b>Цена: {$pr_local['price_old']}</b> {$text_filling['currency']}
<i>{$pr_local['title']}</i>
—————————————————————————
";
                $local_num++;
            }
            if ($quality_row > 5)
                $callback = json_decode($core->sendMessage($local_text, $keyboard->profile_favorite()), true);
            else
                $callback = json_decode($core->sendMessage($local_text), true);
        } else
            $callback = json_decode($core->sendMessage($text_filling['message']['favorite']['null']), true);
        break;

    default:
        if ($bool_via_bot === TRUE)
            if (iconv_strlen($data['text']) == 14) {
                $data['text'] = substr($data['text'], 1);


                $keyboard->keyboard_type = 'inline_keyboard';


                $keyboard->mysqli_result = $local_mysqli_result =
                    $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE {$data['text']}")->fetch();

                $callback = json_decode($core->sendPhoto($local_mysqli_result['title'],
                    $local_mysqli_result['image_id'],
                    $keyboard->other_variation_product()), true);

                $mysqli->query("CALL PC_update('service_id', '{$callback['result']['message_id']}', '$user_id', 'users')");
            }
        break;
}

if ($callback)
    $mysqli->query("CALL PC_update('message_id', '{$callback['result']['message_id']}', '$user_id', 'users')");


$core->deleteMessage($mysqli_result_users['message_id']);

if ($bool_via_bot === FALSE)
    $core->deleteMessage($mysqli_result_users['callback_id']);
$core->deleteMessage($mysqli_result_users['service_id']);