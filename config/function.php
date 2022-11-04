<?php

class API
{
    public int $user_id;
    public string|null $parse_mode = null;
    private string $url;

    public bool $protect_content = FALSE;

    public function __construct($token)
    {
        $this->url = "https://api.telegram.org/bot$token/";
    }

    public function sendMessage($text, $reply_markup = NULL): bool|array|string
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
            'protect_content' => $this->protect_content,
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

    public function epicentrk_main_menu(): bool|string
    {
        if ($this->mysqli_result['is_active'] === 1) {
            $this->add('callback_data', "🪪 Мой бейдж 🪪", 'card_service_id', NULL, 0, 0);
            $this->add('callback_data', "Контроль сроков годности", NULL, NULL, 1, 0);
            $this->add('callback_data', "ОПТ 3% [от 1 тыс]", 'card_opt_3', NULL, 2, 0);
            $this->add('callback_data', "ОПТ 5% [от 3 тыс]", 'card_opt_5', NULL, 2, 1);
            $this->add('callback_data', "ОПТ 10% [от 5 тыс]", 'card_opt_10', NULL, 3, 0);
            $this->add('callback_data', "ОПТ 15% [от 10 тыс]", 'card_opt_15', NULL, 3, 1);
            $this->add('callback_data', "ОПТ 20% [от 15 тыс]", 'card_opt_20', NULL, 4, 0);
            $this->add('callback_data', "ОСББ 15%", 'card_osbb', NULL, 5, 0);
        }
        else
            $this->add('callback_data', "⚠ Запросить доступ ⚠", NULL, NULL, 0, 0);




        return json_encode($this->keyboard);
    }

    public function close(): bool|string
    {
        $this->add('callback_data', "Закрыть", 'close', NULL, 0, 0);
        return json_encode($this->keyboard);
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

    public function search_main_menu(): bool|string
    {
        $this->add('callback_data', $this->text_filling['keyboard']['search']['brand'],
            'search_main_menu', 'brand', 0, 0);

        $this->add('callback_data', $this->text_filling['keyboard']['search']['category'],
            'search_main_menu', 'category', 0, 1);

        $this->add('callback_data', $this->text_filling['keyboard']['search']['list'],
            'search_main_menu', 'list', 1, 0);

        $this->add('callback_data', $this->text_filling['keyboard']['back_main_search'],
            'close', NULL, 2, 0);

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

    public function search_main_product(): bool|string
    {
        $sql_result = $this->mysqli_link->query("
SELECT product.{$this->callback_data_type}_id,
       $this->callback_data_type.description
FROM product
         INNER JOIN $this->callback_data_type ON ($this->callback_data_type.id = product.{$this->callback_data_type}_id)
GROUP BY {$this->callback_data_type}_id, $this->callback_data_type.count_characters ASC");

        $column = 0;
        $row = 0;
        $count = 0;
        $num_rows = 0;

        foreach ($sql_result as $sql_value) {
            $num_rows++;
            if (iconv_strlen($sql_value['description']) <= 11) {
                $count++;
                $this->add('callback_data', $sql_value['description'],
                    $this->callback_data_action, $sql_value[$this->callback_data_type.'_id'], $row, $column);
                $column++;
            } else {
                if ($count >= 1) $row++;
                $column = 0;
                $count = 0;
                $this->add('callback_data', $sql_value['description'],
                    $this->callback_data_action, $sql_value[$this->callback_data_type.'_id'], $row, $column);
                $row++;
            }
            if ($column == 3) {
                $count = 0;
                $column = 0;
                $row++;
            }
        }

        $this->add('callback_data', 'Назад',
            'back_main_search', NULL, $row, 0);

        return json_encode($this->keyboard);
    }


}
