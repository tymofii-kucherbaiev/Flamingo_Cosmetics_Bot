<?php
/**
 * @var $mysqli_result_users mysqli_result
 * @var $mysqli mysqli_result
 * @var $core API
 * @var $keyboard keyboard
 * @var $data
 * @var $user_id integer
 * @var $text_filling array
 */

$core->deleteMessage($data['message_id']);
switch ($data['text']) {
    case $text_filling['command']['start']:
        $back_message = json_decode($core->sendMessage($mysqli_result_users['first_name'] . $text_filling['message']['welcome'],
            $keyboard->main_menu()), true);

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
        # Переработать
        $callback = json_decode($core->sendMessage($text_filling['message']['cart']['null']), true);
        break;

    case $text_filling['command']['help']:
    case $text_filling['keyboard']['main']['help']:
        # Переработать
        $callback = json_decode($core->sendMessage($text_filling['message']['help']), true);
        break;

    case $text_filling['keyboard']['main']['favorite']:
        # Переработать
        $callback = json_decode($core->sendMessage($text_filling['message']['favorite']['null']), true);
        break;

    default:
        if (iconv_strlen($data['text']) == 14) {
            $data['text'] = substr($data['text'], 1);

        }
        break;
}

if ($callback)
    $mysqli->query("CALL PC_update('message_id', '{$callback['result']['message_id']}', '$user_id', 'users')");


$core->deleteMessage($mysqli_result_users['message_id']);