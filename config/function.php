<?php

class api
{
    /* ID пользователя (чата) */
    public int $chat_id;

    /* Режим визуальной разметки */
    public string|null $parse_mode = NULL;

    /* Защищенный просмотр */
    public bool $protect_content = FALSE;

    /* Адрес с токеном */
    private string $url;

    public function __construct($token)
    {
        $this->url = "https://api.telegram.org/bot$token/";
    }

    public function sendMessage($text = 'Hello World', $reply_markup = NULL): bool|array|string
    {
        if ($reply_markup == 'close')
            $request_params = array(
                'chat_id' => $this->chat_id,
                'text' => $text,
                'reply_markup' => json_encode(["remove_keyboard" => true])
            );
        elseif ($this->parse_mode)
            $request_params = array(
                'chat_id' => $this->chat_id,
                'text' => $text,
                'parse_mode' => $this->parse_mode,
                'reply_markup' => $reply_markup
            );
        else
            $request_params = array(
                'chat_id' => $this->chat_id,
                'text' => $text,
                'reply_markup' => $reply_markup
            );
        return $this->curl(method: __FUNCTION__, request_params: $request_params);
    }

    private function curl($method, $request_params): bool|string|array
    {
        $ch = curl_init($this->url . $method . '?');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($request_params));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function sendPhoto($text, $image, $reply_markup = NULL): bool|array|string
    {
        $request_params = array(
            'chat_id' => $this->chat_id,
            'photo' => $image,
            'caption' => $text,
            'protect_content' => $this->protect_content,
            'reply_markup' => $reply_markup);

        return $this->curl(method: __FUNCTION__, request_params: $request_params);
    }

    public function answerInlineQuery($inline_query_id, $result): bool|array|string
    {


        $request_params = array(
            'inline_query_id' => $inline_query_id,
            'is_personal' => false,
            'cache_time' => 1,
            'results' => json_encode($result)
        );
        return $this->curl(method: __FUNCTION__, request_params: $request_params);
    }

    public function answerCallbackQuery($text = NULL, $callback_query_id = NULL, $show_alert = FALSE): bool|array|string
    {
        $request_params = array(
            'text' => $text,
            'show_alert' => $show_alert,
            'callback_query_id' => $callback_query_id
        );
        return $this->curl(method: __FUNCTION__, request_params: $request_params);
    }

    public function editMessageMedia($message_id, $caption, $media, $reply_markup = NULL): void
    {
        $request_params = array(
            'chat_id' => $this->chat_id,
            'message_id' => $message_id,
            'media' => json_encode([
                'type' => 'photo',
                'media' => $media,
                'caption' => $caption
            ]),
            'reply_markup' => $reply_markup
        );

        $this->curl(method: __FUNCTION__, request_params: $request_params);
    }

    public function editMessageText($text, $message_id, $reply_markup = NULL): void
    {

        $request_params = array(
            'chat_id' => $this->chat_id,
            'message_id' => $message_id,
            'text' => $text,
            'parse_mode' => $this->parse_mode,
            'reply_markup' => $reply_markup
        );
        $this->curl(method: __FUNCTION__, request_params: $request_params);
    }

    public function deleteMessage($message_id, $message_type = 'callback'): void
    {
        $request_params = array(
            'chat_id' => $this->chat_id,
            'message_id' => $message_id
        );
        $this->curl(method: __FUNCTION__, request_params: $request_params);
    }

}

class keyboard
{
    public string $keyboard_type = 'keyboard';
    public string $user_id;

    /* construct */
    public bool $one_time_keyboard = false;
    public array|null $mysqli_result;

    /* mysqli_result */
    public object|null $mysqli_link;

    /* mysqli_link */
    public string|null $callback_data_variation;

    /* callback_data */
    public string|null $callback_data_action;
    public string|bool|null $callback_data_type;
    private array|null $text_filling;

    /* Private */
    private array $keyboard;

    public function __construct($text_filling = NULL)
    {
        $this->text_filling = $text_filling;

        $this->keyboard = [
            $this->keyboard_type => [],
            'resize_keyboard' => true,
            'one_time_keyboard' => $this->one_time_keyboard
        ];
    }

    public function product_description(): bool|string
    {
        $this->add(text: "Закрыть", action: 'close', type: 'description', row: 0, col: 0);
        return json_encode($this->keyboard);
    }

    private function add($keyboard_data_type = 'callback_data', $text = NULl, $url = NULL, $action = NULL, $type = NULL,
                         $variation = NULL, $row = NULL, $col = NULL): void
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
                if ($url)
                    $button =
                        [
                            "url" => "tg://user?id=$url",
                            "text" => $text,
                            $keyboard_data_type => "action:$action|type:$type|variation:$variation"
                        ];
                else
                    $button =
                        [
                            "text" => $text,
                            $keyboard_data_type => "action:$action|type:$type|variation:$variation"
                        ];

                $this->keyboard[$this->keyboard_type][$row][$col] = $button;
                break;

            case 'inline_query':
                if ($this->callback_data_type == 'favorite')
                    $button =
                        [
                            "text" => $text,
                            "switch_inline_query_current_chat" => $this->mysqli_result['vendor_code']
                        ];
                else
                    $button =
                        [
                            "text" => $text,
                            "switch_inline_query_current_chat" => $this->mysqli_result[$this->callback_data_type - 1]['vendor_code']
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
        $this->add(text: $this->text_filling['keyboard']['main']['search'], row: 0, col: 0);
        $this->add(text: $this->text_filling['keyboard']['main']['favorite'], row: 1, col: 0);


//        $this->add(NULL, text: $this->text_filling['keyboard']['main']['help'], row: 1, col: 1);
        $this->add(text: $this->text_filling['keyboard']['main']['cart'], row: 1, col: 1);

        $this->add(text: $this->text_filling['keyboard']['main']['profile'], row: 2, col: 0);

        if ($this->mysqli_result['role'] == 'administrator')
            $this->add(text: $this->text_filling['keyboard']['main']['admin'], row: 2, col: 1);

        return json_encode($this->keyboard);
    }

    public function search_main_menu(): bool|string
    {
        $this->add(text: $this->text_filling['keyboard']['search']['brand'], action: 'search_main_menu',
            type: 'brand', row: 0, col: 0);

        $this->add(text: $this->text_filling['keyboard']['search']['category'], action: 'search_main_menu',
            type: 'category', row: 0, col: 1);

        return json_encode($this->keyboard);
    }

    public function search_product_list(): bool|string
    {
        $key = NULL;

        foreach ($this->mysqli_result as $val_key => $value) {
            if ($value['vendor_code'] == $this->mysqli_result[0]['vendor_code'])
                $key = $val_key;
        }

        if ($this->mysqli_result[0]['count'] == $key)
            $next = 0;
        else
            $next = $key + 1;

        if ($key == 0)
            $back = $this->mysqli_result[0]['count'] - 1;
        else
            $back = $key - 1;


        if ($this->mysqli_link->query("SELECT * FROM users_favorite_products WHERE user_id LIKE $this->user_id AND vendor_code LIKE {$this->mysqli_result[$this->callback_data_type - 1]['vendor_code']}")->rowCount() == 1)
            $local_variation_favorite = 'fill';
        else
            $local_variation_favorite = 'null';

        $this->add(text: $this->text_filling['keyboard']['product']['favorite_' . $local_variation_favorite], action: 'product_favorite',
            variation: $this->mysqli_result[$this->callback_data_type - 1]['vendor_code'], row: 0, col: 0);

        $this->add(text: $this->mysqli_result[$this->callback_data_type - 1]['price_old'] . ' ' . $this->text_filling['currency'],
            type: $this->mysqli_result[0]['category_id'], variation: $this->callback_data_variation, row: 0, col: 1);

        $this->add(text: $this->text_filling['keyboard']['product']['cart'], action: 'product_count',
            variation: $this->mysqli_result[$this->callback_data_type - 1]['vendor_code'], row: 0, col: 2);


        $this->add(text: $this->text_filling['keyboard']['product']['description'], action: 'description',
            type: $this->mysqli_result[$this->callback_data_type - 1]['vendor_code'], row: 1, col: 0);

        if ($this->mysqli_result[$this->callback_data_type - 1]['count'] > 1)
            $this->add(keyboard_data_type: 'inline_query', text: $this->text_filling['keyboard']['product']['another_color']
                . ' [' . $this->mysqli_result[$this->callback_data_type - 1]['count'] - 1 . ']',
                type: $this->mysqli_result['vendor_code'], row: 1, col: 1);

        $this->add(text: '⬅', action: 'search_product_list', type: 'back',
            variation: $this->mysqli_result[$back]['vendor_code'], row: 2, col: 0);

        $this->add(text: $this->callback_data_type . ' из ' . count($this->mysqli_result),
            type: $this->callback_data_type, variation: count($this->mysqli_result), row: 2, col: 1);

        $this->add(text: '➡', action: 'search_product_list', type: 'next',
            variation: $this->mysqli_result[$next]['vendor_code'], row: 2, col: 2);

        $this->add(text: 'Назад', action: 'search_main_menu', type: 'card', variation: $this->callback_data_variation, row: 3, col: 0);


        return json_encode($this->keyboard);
    }

    public function search_main_product(): bool|string
    {
        $sql_result = $this->mysqli_link->query("
SELECT product.{$this->callback_data_type}_id,
       $this->callback_data_type.description
FROM product
         INNER JOIN $this->callback_data_type ON ($this->callback_data_type.id = product.{$this->callback_data_type}_id)
GROUP BY {$this->callback_data_type}_id, $this->callback_data_type.count_characters ASC")->fetchAll();

        $column = 0;
        $row = 0;
        $count = 0;
        $num_rows = 0;

        foreach ($sql_result as $sql_value) {
            $num_rows++;
            if (iconv_strlen($sql_value['description']) <= 11) {
                $count++;

                $this->add(text: $sql_value['description'], action: $this->callback_data_action,
                    type: $sql_value[$this->callback_data_type . '_id'], variation: $this->callback_data_type,
                    row: $row, col: $column);

                $column++;
            } else {
                if ($count >= 1) $row++;
                $column = 0;
                $count = 0;

                $this->add(text: $sql_value['description'], action: $this->callback_data_action,
                    type: $sql_value[$this->callback_data_type . '_id'], variation: $this->callback_data_type,
                    row: $row, col: $column);

                $row++;
            }
            if ($column == 3) {
                $count = 0;
                $column = 0;
                $row++;
            }
        }

        $this->add(text: 'Закрыть', action: 'close', row: $row, col: 0);

        return json_encode($this->keyboard);
    }

    public function other_variation_product(): bool|string
    {
        if ($this->mysqli_link->query("SELECT * FROM users_favorite_products WHERE user_id LIKE $this->user_id AND vendor_code LIKE {$this->mysqli_result['vendor_code']}")->rowCount() == 1)
            $local_variation_favorite = 'fill';
        else
            $local_variation_favorite = 'null';

        $this->add(text: $this->text_filling['keyboard']['product']['favorite_' . $local_variation_favorite], action: 'product_favorite', type: 'favorite', variation: $this->mysqli_result['vendor_code'], row: 0, col: 0);

        $this->add(text: $this->mysqli_result['price_old'] . ' ' . $this->text_filling['currency'], row: 0, col: 1);

        $this->add(text: $this->text_filling['keyboard']['product']['cart'], action: 'product_count', type: 'cart', variation: $this->mysqli_result['vendor_code'], row: 0, col: 2);

        $this->add(text: $this->text_filling['keyboard']['back_main_search'], action: 'close', type: 'extra', row: 1, col: 0);

        return json_encode($this->keyboard);
    }


    public function ordering(): bool|string
    {
        switch ($this->callback_data_variation) {

            case 'set_delivery':
                $this->add(text: 'ТРЦ Золотое Кольцо', action: 'ordering', type: 'golden_ring', variation: 'set_comment', row: 0, col: 0);
                $this->add(text: 'ТРЦ Донецк Сити', action: 'ordering', type: 'donetsk_city', variation: 'set_comment', row: 0, col: 1);
                $this->add(text: $this->text_filling['keyboard']['ordering']['cancel'], action: 'close', type: 'cart', row: 1, col: 0);
                break;

            case 'set_confirm':
            case 'remember_on';

            if ($this->callback_data_type === TRUE)
                $this->add(text: $this->text_filling['keyboard']['ordering']['edit'], action: 'ordering', variation: 'set_edit', row: 0, col: 0);
            else
                $this->add(text: $this->text_filling['keyboard']['ordering']['remember_off'], action: 'ordering', variation: 'remember_off', row: 0, col: 0);


            $this->add(text: $this->text_filling['keyboard']['ordering']['confirm'], action: 'order_confirm', row: 0, col: 1);
                $this->add(text: $this->text_filling['keyboard']['ordering']['cancel'], action: 'close', type: 'cart', row: 1, col: 0);
                break;

            case 'remember_off':
                $this->add(text: $this->text_filling['keyboard']['ordering']['remember_on'], action: 'ordering', variation: 'remember_on', row: 0, col: 0);
                $this->add(text: $this->text_filling['keyboard']['ordering']['confirm'], action: 'order_confirm', row: 0, col: 1);
                $this->add(text: $this->text_filling['keyboard']['ordering']['cancel'], action: 'close', type: 'cart', row: 1, col: 0);
                break;

            case 'set_comment':
                $this->add(text: $this->text_filling['keyboard']['ordering']['skip'], action: 'ordering', variation: 'set_confirm', row: 0, col: 0);
                $this->add(text: $this->text_filling['keyboard']['ordering']['cancel'], action: 'close', type: 'cart', row: 1, col: 0);
                break;

            default:
                $this->add(text: $this->text_filling['keyboard']['ordering']['cancel'], action: 'close', type: 'cart', row: 0, col: 0);
                break;
        }
        return json_encode($this->keyboard);
    }

    public function count_product_cart(): bool|string
    {
        $this->add(text: $this->text_filling['keyboard']['number']['1'], action: 'product_cart', type: $this->callback_data_type, variation: 1, row: 0, col: 0);
        $this->add(text: $this->text_filling['keyboard']['number']['2'], action: 'product_cart', type: $this->callback_data_type, variation: 2, row: 0, col: 1);
        $this->add(text: $this->text_filling['keyboard']['number']['3'], action: 'product_cart', type: $this->callback_data_type, variation: 3, row: 0, col: 2);
        $this->add(text: $this->text_filling['keyboard']['number']['4'], action: 'product_cart', type: $this->callback_data_type, variation: 4, row: 0, col: 3);
        $this->add(text: $this->text_filling['keyboard']['number']['5'], action: 'product_cart', type: $this->callback_data_type, variation: 5, row: 0, col: 4);
        $this->add(text: $this->text_filling['keyboard']['number']['6'], action: 'product_cart', type: $this->callback_data_type, variation: 6, row: 0, col: 5);
        $this->add(text: $this->text_filling['keyboard']['number']['7'], action: 'product_cart', type: $this->callback_data_type, variation: 7, row: 0, col: 6);

        return json_encode($this->keyboard);
    }

    public function edit_order(): bool|string
    {
        $i = 1;
        foreach ($this->mysqli_result as $item) {
            $this->add(text: '№ ' . $i, row: $i - 1, col: 0);
            $this->add(text: $this->text_filling['keyboard']['order']['delete'], action: 'delete_product',
                type: $item['vendor_code'], variation: $item['quality'], row: $i - 1, col: 1);

            if ($item['modify_quality'] > 1)
                $this->add(text: $this->text_filling['keyboard']['order']['remove'], action: 'remove_product',
                    type: $item['vendor_code'], variation: $item['quality'], row: $i - 1, col: 2);
            else
                $this->add(text: $this->text_filling['keyboard']['order']['minimum_count'], row: $i - 1, col: 2);

            $this->add(text: $this->text_filling['keyboard']['order']['add'], action: 'add_product',
                type: $item['vendor_code'], variation: $item['quality'], row: $i - 1, col: 3);
            $i++;
        }

        $this->add(text: $this->text_filling['keyboard']['order']['cancel'], action: 'back_cart', type: 'cancel', row: $i - 1, col: 0);
        $this->add(text: $this->text_filling['keyboard']['order']['apply'], action: 'back_cart', type: 'apply', row: $i - 1, col: 1);

        return json_encode($this->keyboard);
    }

    /* Cart and Favorite */

    public function profile_list(): bool|string
    {
        $this->add(text: $this->text_filling['keyboard']['cart']['edit_cart'], action: 'edit_cart', row: 0, col: 0);
        $this->add(text: $this->text_filling['keyboard']['cart']['ordering'], action: 'ordering', variation: 'set_name', row: 0, col: 1);

        return json_encode($this->keyboard);
    }

    public function profile_favorite(): bool|string
    {
        if ($this->callback_data_action == 'primary')
            $this->add(text: $this->text_filling['keyboard']['favorite']['next'], action: 'favorite_next', row: 0, col: 0);
        else {
            $this->add(text: $this->text_filling['keyboard']['favorite']['back'], action: 'favorite_back', row: 0, col: 0);
            $this->add(text: $this->text_filling['keyboard']['favorite']['back'], action: 'favorite_back', row: 0, col: 1);
        }

        return json_encode($this->keyboard);
    }

    public function product_card(): bool|string
    {

        if ($this->mysqli_link->query("SELECT * FROM users_favorite_products WHERE user_id LIKE $this->user_id AND vendor_code LIKE {$this->mysqli_result['vendor_code']}")->rowCount() == 1)
            $local_variation_favorite = 'fill';
        else
            $local_variation_favorite = 'null';

        $this->add(text: $this->text_filling['keyboard']['product']['favorite_' . $local_variation_favorite], action: 'product_favorite',
            variation: $this->mysqli_result['vendor_code'], row: 0, col: 0);

        $this->add(text: $this->mysqli_result['price_old'] . ' ' . $this->text_filling['currency'],
            type: $this->mysqli_result['category_id'], row: 0, col: 1);

        $this->add(text: $this->text_filling['keyboard']['product']['cart'], action: 'product_count',
            variation: $this->mysqli_result['vendor_code'], row: 0, col: 2);

        $this->add(text: $this->text_filling['keyboard']['product']['description'], action: 'description',
            type: $this->mysqli_result['vendor_code'], row: 1, col: 0);
        //
        $this->add(text: $this->text_filling['keyboard']['back_main_search'], action: 'close', type: 'favorite', row: 2, col: 0);

        return json_encode($this->keyboard);
    }


    public function admin_order_control(): bool|string
    {
        $this->add(text: '✅ Взять в работу', action: 'take_to_work', row: 0, col: 0);

        return json_encode($this->keyboard);
    }
}

class other
{
    public array|null $mysqli_result;
    public object|null $mysqli_link;
    public array|null $text_filling;

    public function profile_list($action = FALSE): string
    {
        $local_text = "Ваша корзина:\n";
        $local_sum = 0;
        $local_num = 1;

        foreach ($this->mysqli_result as $value) {
            if ($action === TRUE)
                $quality = $value['modify_quality'];
            else
                $quality = $value['quality'];

            $pr_local = $this->mysqli_link->query("SELECT * FROM product WHERE vendor_code LIKE {$value['vendor_code']}")->fetch();
            $local_sum = $local_sum + ($pr_local['price_old'] * $quality);

            $local_text .= "
—————————————————————————
<b>№$local_num   /{$pr_local['vendor_code']}</b>  <b>$quality шт.</b>  <b>Цена: {$pr_local['price_old']}</b> {$this->text_filling['currency']}
<i>{$pr_local['title']}</i>
—————————————————————————
";
            $local_num++;
        }

        if ($local_sum < 1000) {
            $local_text .= "\n <b>🛒 Сумма заказа:</b> $local_sum {$this->text_filling['currency']}";
            $local_text .= "\n <b>📦 Доставка:</b> {$this->text_filling['delivery_price']} {$this->text_filling['currency']} (Беслпатная от {$this->text_filling['delivery_free']} {$this->text_filling['currency']})";
            $local_sum = $local_sum + $this->text_filling['delivery_price'];
        } else
            $local_text .= "\n <b>📦 Доставка: 🆓 Бесплатно 🆓</b>";

        $local_text .= "\n <b>💳 К оплате:</b> $local_sum {$this->text_filling['currency']}";
        return $local_text;
    }
}
