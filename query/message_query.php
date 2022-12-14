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
 */

$core->deleteMessage($data['message_id']);
switch ($data['text']) {
    case $text_filling['command']['start']:
        $callback_local = json_decode($core->sendMessage($mysqli_result_users['first_name'] . $text_filling['message']['welcome'],
            $keyboard->main_menu()), true);
        $core->deleteMessage($mysqli_result_users['main_menu_id']);
        $mysqli->query("CALL PC_update('main_menu_id = \'{$callback_local['result']['message_id']}\', order_position = NULL', '$user_id', 'users')");
        $core->deleteMessage($mysqli_result_users['message_id']);
        break;

    case $text_filling['command']['search']:
    case $text_filling['keyboard']['main']['search']:
        $keyboard->keyboard_type = 'inline_keyboard';
        $keyboard->callback_data_action = 'search_product_list';

        $keyboard->callback_data_type = 'category';
        $callback = json_decode($core->sendMessage($text_filling['message']['search']['callback_category'],
            $keyboard->search_main_product()), true);
        $mysqli->query("CALL PC_update('callback_id = \'{$callback['result']['message_id']}\'', '$user_id', 'users')");
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
        $local_user_result = $mysqli->query("SELECT * FROM users_favorite_products WHERE user_id LIKE $user_id")->fetchAll();

        if ($local_user_result) {

            $quality_row = count($local_user_result);
            if ($quality_row > 5) {
                $keyboard->keyboard_type = 'inline_keyboard';
                $keyboard->callback_data_action = 'primary';
            }

            $local_text = "Ваш список желаемого:\n";
            $local_num = 1;

            foreach ($local_user_result as $value) {
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
        $core->deleteMessage($mysqli_result_users['message_id']);
        break;

    default:
        if ($mysqli_result_users['order_position']) {
            $local_variation = NULL;
            $set_table = NULL;
            $complete = FALSE;
            switch ($mysqli_result_users['order_position']) {
                case 'set_name':

                    $explode_full_name = explode(' ', $data['text']);
                    if (!$explode_full_name[1]) {
                        $local_callback = json_decode($core->sendMessage($text_filling['message']['error_order']['set_name']), TRUE);
                        $mysqli->query("CALL PC_update('service_id = \'{$local_callback['result']['message_id']}\'', '$user_id', 'users')");
                    } else {
                        $complete = TRUE;

                        $local_variation = 'set_phone';
                        $set_table = "profile_first_name = \'$explode_full_name[0]\', profile_last_name = \'$explode_full_name[1]\'";
                    }

                    break;

                case 'set_phone':
                        $local_variation = 'set_delivery';

                        if (preg_match('/^[0-9]+$/i', $data['text']) == 0 OR iconv_strlen($data['text']) != 12) {
                            $local_callback = json_decode($core->sendMessage($text_filling['message']['error_order']['set_phone']), TRUE);
                            $mysqli->query("CALL PC_update('service_id = \'{$local_callback['result']['message_id']}\'', '$user_id', 'users')");
                        } else {
                            $complete = TRUE;

                            $set_table = 'phone_number = ' . $data['text'];
                        }
                    break;

                case 'set_delivery':
                    $local_callback = json_decode($core->sendMessage($text_filling['message']['error_order']['set_delivery']), TRUE);
                    $mysqli->query("CALL PC_update('service_id = \'{$local_callback['result']['message_id']}\'', '$user_id', 'users')");
                    break;

                case 'set_comment':

                    break;
            }
            if ($complete) {

                $keyboard->mysqli_result = $mysqli->query("CALL PC_update('$set_table, order_position = \'{$local_variation}\'', '$user_id', 'users')")->fetch();
                $keyboard->callback_data_variation = $local_variation;
                $keyboard->keyboard_type = 'inline_keyboard';

                $core->editMessageText($text_filling['message']['order'][$local_variation], $mysqli_result_users['message_id'], $keyboard->ordering());
            }
        }
        if (1 == 0) {
            if (iconv_strlen($data['text']) == 14) {
                $data['text'] = substr($data['text'], 1);
                $keyboard->keyboard_type = 'inline_keyboard';
                if ($bool_via_bot === TRUE) {


                    $keyboard->mysqli_result = $local_mysqli_result =
                        $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE {$data['text']}")->fetch();

                    $callback = json_decode($core->sendPhoto($local_mysqli_result['title'],
                        $local_mysqli_result['image_id'],
                        $keyboard->other_variation_product()), true);

                    $mysqli->query("CALL PC_update('service_id = \'{$callback['result']['message_id']}\'', '$user_id', 'users')");
                } else {
                    $keyboard->callback_data_action = $data['text'];
                    $keyboard->callback_data_type = 'favorite';


                    $keyboard->mysqli_result = $mysqli_product_card =
                        $mysqli->query("SELECT * FROM product WHERE vendor_code LIKE {$data['text']}")->fetch();

                    $callback = json_decode($core->sendPhoto($mysqli_product_card['title'], $mysqli_product_card['image_id'], $keyboard->product_card()), true);
                }
            }
            $core->deleteMessage($mysqli_result_users['message_id']);
        }
        break;
}

if ($callback)
    $mysqli->query("CALL PC_update('message_id = \'{$callback['result']['message_id']}\', order_position = NULL', '$user_id', 'users')");


if ($bool_via_bot === FALSE)
    $core->deleteMessage($mysqli_result_users['callback_id']);

$core->deleteMessage($mysqli_result_users['service_id']);
