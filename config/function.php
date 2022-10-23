<?php

class API
{
    public int $user_id;
    public string|null $parse_mode = null;
    private string $url;

    public function __construct($token)
    {
        $this->url = "https://api.telegram.org/bot$token/";
    }

    public function sendMessage($text, $reply_markup): bool|array|string
    {
        if ($reply_markup == 'close')
            $request_params = array(
                'chat_id' => $this->user_id,
                'text' => $text,
                'reply_markup' => json_encode(["remove_keyboard" => true])
            );
        elseif ($this->parse_mode)
            $request_params = array(
                'chat_id' => $this->user_id,
                'text' => $text,
                'parse_mode' => $this->parse_mode,
                'reply_markup' => $reply_markup
            );
        else
            $request_params = array(
                'chat_id' => $this->user_id,
                'text' => $text,
                'reply_markup' => $reply_markup
            );
        return $this->error($this->curl(method: __FUNCTION__, request_params: $request_params));
    }

    private function error($input)
    {
        $error = json_decode($input, true);
        if ($error['ok'] === FALSE)
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/errors/' .
                $error['error_code'] . ' [' . date("d-m") . '] [' . date("H-i-s") . '].json', $input);
        return $input;
    }

    public function curl($method, $request_params): bool|string|array
    {
        $ch = curl_init($this->url . $method . '?');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($request_params));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function sendPhoto($text, $image, $reply_markup): bool|array|string
    {
        $request_params = array(
            'chat_id' => $this->user_id,
            'photo' => $image,
            'caption' => $text,
//            'parse_mode' => 'MarkdownV2',
            'reply_markup' => $reply_markup);

        return $this->error($this->curl(method: __FUNCTION__, request_params: $request_params));
    }

    public function answerInlineQuery($inline_query_id, $result): bool|array|string
    {


        $request_params = array(
            'inline_query_id' => $inline_query_id,
            'is_personal' => true,
//            'switch_pm_text' => 'switch_pm_text',
//            'switch_pm_parameter' => 'switch_pm_parameter',
            'cache_time' => 0,
            'results' => json_encode($result)
        );
        return $this->error($this->curl(method: __FUNCTION__, request_params: $request_params));
    }

    public function answerCallbackQuery($text, $show_alert, $callback_query_id): bool|array|string
    {
        $request_params = array(
            'text' => $text,
            'show_alert' => $show_alert,
            'callback_query_id' => $callback_query_id
        );
        return $this->error($this->curl(method: __FUNCTION__, request_params: $request_params));
    }

    public function editMessageText($text, $message_id, $reply_markup): bool|array|string
    {

        $request_params = array(
            'chat_id' => $this->user_id,
            'message_id' => $message_id,
            'text' => $text,
            'reply_markup' => $reply_markup
        );
        return $this->error($this->curl(method: __FUNCTION__, request_params: $request_params));
    }

    public function editMessageMedia($caption, $message_id, $image, $reply_markup): bool|array|string
    {
        $photo = ['type' => 'photo',
            'media' => $image,
            'caption' => $caption,
//            'parse_mode' => 'html'
        ];

        $request_params = array(
            'chat_id' => $this->user_id,
            'message_id' => $message_id,
            'media' => json_encode($photo),
            'reply_markup' => $reply_markup
        );
        return $this->error($this->curl(method: __FUNCTION__, request_params: $request_params));
    }

    public function deleteMessage($message_id): void
    {
        $request_params = array(
            'chat_id' => $this->user_id,
            'message_id' => $message_id
        );
        $this->error($this->curl(method: __FUNCTION__, request_params: $request_params));
    }

//    public function sendLocation ($chat_id, $latitude, $longitude): void
//    {
//        $this->sendMessage("Покупатель: Гитлер Адольф Константинович
//Телефон: +38-050-000-00-00
//
//Заказ: №4213
//Содержимое:
//1. FFLEUR TK-12 №04 Пудра компактная «2в1″ - 2 шт - 200 грн (400 грн)
//2. FFLEUR TK-12 №02 Пудра компактная «2в1″ - 1 шт - 200 грн (200 грн)
//
//Сумма: 600 грн
//Адресс доставки: ", $chat_id, NULL);
//        $request_params = array(
//            'chat_id' => $chat_id,
//            'protect_content' => false,
//            'latitude' => $latitude,
//            'longitude' => $longitude
//        );
//        $this->curl(method: __FUNCTION__, request_params: $request_params);
//    }

}

class keyboard
{
    public string $keyboard_type = 'keyboard';

    /* construct */
    public bool $one_time_keyboard = false;
    public array|null $text_filling;

    /* text */
    public array|null $mysqli_result;

    /* mysqli */
    public object|null $mysqli_link;
    public string|null $callback_data_action;

    /* callback_data */
    public string|null $callback_data_type;
    private array $keyboard;

    public function __construct()
    {
        $this->keyboard = [
            $this->keyboard_type => [],
            'resize_keyboard' => true,
            'one_time_keyboard' => $this->one_time_keyboard
        ];
    }

    private function add($keyboard_data_type, $text, $action, $type, $row, $col): void
    {
        switch ($keyboard_data_type) {
            case 'request_contact':
            case 'request_location':
                $button =
                    [
                        "text" => $text,
                        $keyboard_data_type => $type
                    ];

                $this->keyboard[$this->keyboard_type][$row][$col] = $button;
                break;

            case 'callback_data':
                $button =
                    [
                        "text" => $text,
                        $keyboard_data_type => "action:$action|type:$type"
                    ];

                $this->keyboard[$this->keyboard_type][$row][$col] = $button;
                break;

            case 'inline_query':
                $button =
                    [
                        "text" => $text,
                        "switch_inline_query_current_chat" => "Hello"
                    ];

                $this->keyboard[$this->keyboard_type][$row][$col] = $button;
                break;

            default:
                $button =
                    [
                        "text" => $text
                    ];

                $this->keyboard[$this->keyboard_type][$row][$col] = $button;
                break;
        }
    }

    public function epicentrk_product(): bool|string
    {
        $this->add('callback_data', "Товара нет!", 'not_available', $this->mysqli_result['vendor_code'], 0, 0);
        $this->add('callback_data', "Изменить даты", 'change_dates', $this->mysqli_result['vendor_code'], 0, 1);
        $this->add('callback_data', "Осталось дней: " . $this->mysqli_result['days_left'], NULL, NULL, 1, 0);
        $this->add('callback_data', "Закрыть", 'close', NULL, 1, 0);

        return json_encode($this->keyboard);
    }



    public function epicentrk_product_date(): bool|string
    {
        $this->add('callback_data', "1. " . $this->mysqli_result['date_1'], 'date_1', $this->mysqli_result['vendor_code'], 0, 0);
        $this->add('callback_data', "2. " . $this->mysqli_result['date_2'], 'date_2', $this->mysqli_result['vendor_code'], 0, 1);
        $this->add('callback_data', "3. " . $this->mysqli_result['date_3'], 'date_3', $this->mysqli_result['vendor_code'], 1, 0);
        $this->add('callback_data', "4. " . $this->mysqli_result['date_4'], 'date_4', $this->mysqli_result['vendor_code'], 1, 1);
        $this->add('callback_data', "Закрыть", 'close', NULL, 2, 0);

        return json_encode($this->keyboard);
    }

    public function main_menu(): bool|string
    {
        $i = 0;
        $col = 0;
        $row = 0;

        if ($this->mysqli_result['phone_number']) {
            $this->add(NULL, $this->text_filling['keyboard']['main']['search'], NULL, NULL, $row, $col);
            $row++;
        }

        if ($this->mysqli_result['cart_product']) {
            $i++;
            $this->add(NULL, $this->text_filling['keyboard']['main']['cart'], NULL, NULL, $row, $col);
            $col++;
        }

        if ($this->mysqli_result['role'] != 'viewer') {
            $i++;
            $this->add(NULL, $this->text_filling['keyboard']['main']['admin'], NULL, NULL, $row, $col);
            $col++;
        }

        if ($this->mysqli_result['favorite']) {
            $i++;
            $this->add(NULL, $this->text_filling['keyboard']['main']['favorite'], NULL, NULL, $row, $col);
        }

        if ($i != 0) $row++;

        if ($this->mysqli_result['phone_number'])
            $this->add(NULL, $this->text_filling['keyboard']['main']['profile'], NULL, NULL, $row, 0);
        else
            $this->add('request_contact', $this->text_filling['keyboard']['main']['login'], NULL, true, $row, 0);

        $this->add(NULL, $this->text_filling['keyboard']['main']['help'], NULL, NULL, $row, 1);
        return json_encode($this->keyboard);
    }

    public function search_menu(): bool|string
    {
        $this->add('callback_data', $this->text_filling['keyboard']['search']['brand'],
            $this->text_filling['callback_data']['action']['product_brand'], NULL, 0, 0);

        $this->add('callback_data', $this->text_filling['keyboard']['search']['category'],
            $this->text_filling['callback_data']['action']['product_category'], NULL, 0, 1);

        $this->add('callback_data', $this->text_filling['keyboard']['search']['list'],
            $this->text_filling['callback_data']['action']['product_list'], NULL, 1, 0);

        return json_encode($this->keyboard);
    }

    public function product_card($product_card): bool|string
    {

        $this->add('callback_data', '⭐', 'add_favorite', NULL, 0, 0);
        $this->add('callback_data', $this->mysqli_result['price_old'] . ' ' . $this->text_filling['currency'], NULL, NULL, 0, 1);
        $this->add('callback_data', '🛒', 'add_cart', NULL, 0, 2);


        $this->add('callback_data', 'Описание', 'description', $this->mysqli_result['vendor_code'], 1, 0);
        $this->add('inline_query', 'Выбрать другой цвет', 'description', $this->mysqli_result['vendor_code'], 1, 1);

        $this->add('callback_data', '⬅', 'page_prev', NULL, 2, 0);
        $this->add('callback_data', '1 из 40', NULL, NULL, 2, 1);
        $this->add('callback_data', '➡', 'page_next', NULL, 2, 2);

        $this->add('callback_data', 'Назад', 'back', NULL, 3, 0);

        return json_encode($this->keyboard);
    }

    public function catalog(): bool|string
    {

        $sql_result = $this->mysqli_link->query("SELECT * FROM $this->callback_data_action ORDER BY $this->callback_data_action.count_characters ASC");
        $column = 0;
        $row = 0;
        $count = 0;
        $num_rows = 0;

        foreach ($sql_result as $sql_value) {
            $num_rows++;
            if (iconv_strlen($sql_value['description']) <= 11) {
                $count++;
                $this->add('callback_data',
                    $sql_value['description'],
                    $this->callback_data_action, $sql_value['id'], $row, $column);
                $column++;
            } else {
                if ($count >= 1) $row++;
                $column = 0;
                $count = 0;
                $this->add('callback_data',
                    $sql_value['description'],
                    $this->callback_data_action, $sql_value['id'], $row, $column);
                $row++;
            }
            if ($column == 3) {
                $count = 0;
                $column = 0;
                $row++;
            }
        }
        $this->add('callback_data',
            $this->text_filling['callback_data']['keyboard']['back_main_search'],
            'back_main_search', NULL, $row, 0);
        return json_encode($this->keyboard);
    }


    /*




    //    private function test ($result_sql): bool|string
    //    {
    //
    //        $col = 0; $row = 0; $i = 0;
    //
    //        foreach ($result_sql as $sql_value) {
    ////            if ($sql_value['Brand'] == 'CATRICE') {
    ////                if ($i == 0) {
    ////                    $i = 0;
    //                    $this->add('callback_data',
    //                        $sql_value['Brand'],
    //                        $sql_value['Brand'], NULL, $row, $col);
    //                    $col++;
    //                    if ($col == 2) {
    //                        $col = 0;
    //                        $row++;
    //                    }
    ////                }
    //
    //
    //
    ////                $i++;
    ////            }
    //        }
    //
    //        return json_encode($this->keyboard);
    //    }

    //    private function test ($result_sql): bool|string
    //    {
    //
    //        $col = 0; $row = 0; $i = 0;
    //
    //        $res = explode ('|', $text);
    //        $val = explode (':', $res[0]);
    //
    //        foreach ($result_sql as $sql_value) {
    ////            if ($sql_value['Brand'] == $val[1]) {
    //            foreach (explode(', ', $sql_value['Category']) as $value) {
    //
    //
    //
    //
    //
    //
    ////            if ($sql_value['Brand'] == 'CATRICE') {
    ////                if ($i == 0) {
    ////                    $i = 0;
    //                    $this->add('callback_data',
    //                        $value,
    //                        NULl, NULL, $row, $col);
    //                    $col++;
    //                    if ($col == 2) {
    //                        $col = 0;
    //                        $row++;
    //                    }
    ////                }
    //
    //
    //
    ////                $i++;
    ////            }
    ////            break;
    //            }
    //        }
    //
    //        return json_encode($this->keyboard);
    //    }
    */
}
