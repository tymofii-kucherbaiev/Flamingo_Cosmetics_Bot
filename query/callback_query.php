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
        $keyboard->callback_data_variation = $callback_variation;
        $keyboard->callback_data_type = $callback_type;

        $core->editMessageText($text_filling['message']['search'][''], $mysqli_result_users['callback_id'],
            $keyboard->search_product_list());
        break;

    case 'back_main_search':
        $keyboard->keyboard_type = 'inline_keyboard';
        $core->editMessageText($text_filling['message']['search'], $mysqli_result_users['callback_id'], $keyboard->search_main_menu());
        break;

    case 'close':
        $core->deleteMessage($mysqli_result_users['callback_id']);
        break;
}
