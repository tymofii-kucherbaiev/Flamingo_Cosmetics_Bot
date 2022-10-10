<?php
class API
{
    private string $url;


    public function __construct ($token)
    {
        $this->url = "https://api.telegram.org/bot$token/";
    }
///////////////////////////////////////////////////////////////////////
    public function curl ($method, $request_params): bool|string|array
    {
        $ch = curl_init($this->url . $method . '?');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($request_params));
        $result =  curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function sendMessage ($text, $chat_id, $reply_markup): bool|array|string
    {
        if ($reply_markup == 'close')
            $request_params = array (
                'chat_id' => $chat_id,
                'text' => $text,
                'reply_markup' => json_encode(["hide_keyboard" => true])
            );
        else
            $request_params = array (
                'chat_id' => $chat_id,
                'text' => $text,
                'parse_mode' => 'MarkdownV2',
                'reply_markup' => $reply_markup
            );
        return $this->curl(method: __FUNCTION__, request_params: $request_params);
    }

    public function sendPhoto ($text, $chat_id, $reply_markup)
    {
        $request_params = array (
            'chat_id' => $chat_id,
//            'photo' => "https://i0.wp.com/dianomi-dn.com/wp-content/uploads/2022/08/590125b0-c982-11ec-80c9-9c8e99520657_b4e69b3b-e7e3-11ec-80ca-9c8e99520657.jpeg?fit=480%2C480&ssl=1",
            'photo' => "AgACAgQAAxkDAAIHVWM1VVDKHDOtNwpTrMFJm-_6-YPQAAIjrzEbpxalURxUqDnu7r0gAQADAgADcwADKgQ",
            'caption' => $text,
            'parse_mode' => 'MarkdownV2',
            'reply_markup' => $reply_markup);

        return $this->curl(method: __FUNCTION__, request_params: $request_params);
    }

    public function answerCallbackQuery ($text, $show_alert, $callback_query_id): void
    {
        $request_params = array(
            'text' => $text,
            'show_alert' => $show_alert,
            'callback_query_id' => $callback_query_id
        );
        $this->curl(method: __FUNCTION__, request_params: $request_params);
    }

    public function editMessageText ($text, $chat_id, $message_id, $reply_markup): bool|array|string
    {
        $request_params = array(
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => $text,
            'reply_markup' => $reply_markup
        );
        return $this->curl(method: __FUNCTION__, request_params: $request_params);
    }

    public function deleteMessage ($chat_id, $message_id): void
    {
        $request_params = array(
            'chat_id' => $chat_id,
            'message_id' => $message_id
        );
        $this->curl(method: __FUNCTION__, request_params: $request_params);
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


class Keyboard
{
    private array $keyboard;
    private string $keyboard_type;


    public function __construct ($keyboard_type, $one_time_keyboard)
    {
        $this->keyboard_type = $keyboard_type;
        $this->keyboard = array($this->keyboard_type => array(),
            'resize_keyboard' => true,
            'one_time_keyboard' => $one_time_keyboard);
    }

    public function add ($keyboard_type, $text, $action, $type, $row, $col): void
    {
        switch ($keyboard_type) {
            case 'request_contact':
            case 'request_location':
                $button =
                    ["text" => $text,
                        $keyboard_type => $type];

                $this->keyboard[$this->keyboard_type][$row][$col] = $button;
                break;

            case 'callback_data':
                $button =
                    ["text" => $text,
                        $keyboard_type => "action:$action|type:$type"];

                $this->keyboard[$this->keyboard_type][$row][$col] = $button;
                break;

            default:
                $button =
                    ["text" => $text];

                $this->keyboard[$this->keyboard_type][$row][$col] = $button;
                break;
        }
    }

    public function get (): bool|string
    {
        return json_encode($this->keyboard);
    }

    public function auto_create ($keyboard, $text_keyboard, $sql_result, $action, $type): bool|string|null
    {
        $result = NULL;
        switch ($keyboard) {
            case 'main_menu':
                $result = $this->main_menu($text_keyboard, $sql_result);
                break;

            case 'calendar':
                $result = $this->calendar();
                break;

            case 'user_account':
                $result = $this->user_account($text_keyboard, $sql_result);
                break;

            case 'catalog':
                $result = $this->catalog();
                break;

            case 'catalog_search':
                $result = $this->catalog_search($text_keyboard);
                break;

            case 'admin_main':
                $result = $this->admin_main($text_keyboard);
                break;

            case 'admin_product_add':
                $result = $this->admin_product_add($text_keyboard);
                break;

            case 'test':
                $result = $this->test($sql_result);
                break;

            case 'message_test':
                $result = $this->message_test($sql_result, $action);
                break;
        }

        return $result;
    }

    private function admin_main ($text_keyboard): bool|string
    {
        $this->add('callback_data', '🟢 Добавить 🟢', NULL, NULL, 0, 0);
        $this->add('callback_data', 'Товар', 'product_add', NULL, 1, 0);
        $this->add('callback_data', 'Категорию', 'product_category', NULL, 1, 1);
        $this->add('callback_data', 'Бренд', 'product_brand', NULL, 1, 2);
        $this->add('callback_data', '🔴 Удалить 🔴', NULL, NULL, 2, 0);
        $this->add('callback_data', 'Товар', NULL, NULL, 3, 0);
        $this->add('callback_data', 'Категорию', NULL, NULL, 3, 1);
        $this->add('callback_data', 'Бренд', NULL, NULL, 3, 2);
        $this->add('callback_data', '🟡 Заказы 🟡', NULL, NULL, 4, 0);
        $this->add('callback_data', 'Новые', 'new_order', NULL, 5, 0);
        $this->add('callback_data', 'Обработанные', 'history_order', NULL, 5, 1);
        $this->add('callback_data', 'Закрыть', 'close', NULL, 6, 0);
        return $this->get();
    }

    private function admin_product_add ($text_keyboard): bool|string
    {
        $this->add('callback_data', '🔺', 'admin_back', NULL, 0, 0);
        $this->add('callback_data', '3 шт.', 'admin_back', NULL, 0, 1);
        $this->add('callback_data', '🔻', 'admin_back', NULL, 0, 2);
        $this->add('callback_data', '⬅', 'admin_back', NULL, 1, 0);
        $this->add('callback_data', '1/40', 'admin_back', NULL, 1, 1);
        $this->add('callback_data', '➡', 'next', NULL, 1, 2);
        return $this->get();
    }

    private function message_test ($text_keyboard, $action): bool|string
    {
        $this->add('callback_data', '⬅', $action, 'color_back', 0, 0);
        $this->add('callback_data', 'Цвет: 010', 'admin_back', NULL, 0, 1);
        $this->add('callback_data', '➡', $action, 'color_next', 0, 2);

        $this->add('callback_data', '⭐', 'next', NULL, 1, 0);
        $this->add('callback_data', '343 ₽', 'next', NULL, 1, 1);
        $this->add('callback_data', '🛒', 'next', NULL, 1, 2);

        $this->add('callback_data', '⬅', 'admin_back', NULL, 2, 0);
        $this->add('callback_data', 'Страница 1 из 40', 'admin_back', NULL, 2, 1);
        $this->add('callback_data', '➡', 'next', NULL, 2, 2);

        return $this->get();
    }

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
//        return $this->get();
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
//        return $this->get();
//    }

    private function main_menu ($text_keyboard, $sql_result): bool|string
    {
        $i = 0; $col = 0; $row = 0;

        if ($sql_result['phone_number']) {
            $this->add(NULL, $text_keyboard['main_search'], NULL, NULL, $row, $col);
            $row++;
        }

        if ($sql_result['cart_product']) {
            $i++;
            $this->add(NULL, $text_keyboard['main_cart'], NULL, NULL, $row, $col);
            $col++;
        }

        if ($sql_result['role'] == 'administrator') {
            $i++;
            $this->add(NULL, $text_keyboard['main_admin'], NULL, NULL, $row, $col);
            $col++;
        }

        if ($sql_result['favorite']) {
            $i++;
            $this->add(NULL, $text_keyboard['main_favorite'], NULL, NULL, $row, $col);
        }

        if ($i != 0) $row++;

        if ($sql_result['phone_number'])
            $this->add(NULL, $text_keyboard['main_profile'], NULL, NULL, $row, 0);
        else
            $this->add('request_contact', $text_keyboard['main_login'], NULL, true, $row, 0);

        $this->add(NULL, $text_keyboard['main_help'], NULL, NULL, $row, 1);
        return $this->get();
    }

    private function user_account ($text_keyboard, $sql_result): bool|string
    {
        $this->add('callback_data', $text_keyboard['profile_history'], NULL, NULL, 0, 0);

        if ($sql_result['profile_name'] == NULL)
            $this->add('callback_data', $text_keyboard['profile_name_unknown'], NULL, NULL, 1, 0);
        else
            $this->add('callback_data', $text_keyboard['profile_name'], NULL, NULL, 1, 0);

        if ($sql_result['sex'] == NULL)
            $this->add('callback_data', $text_keyboard['profile_sex_unknown'], NULL, NULL, 1, 1);
        else
            $this->add('callback_data', $text_keyboard['profile_sex'], NULL, NULL, 1, 1);

        if ($sql_result['birthday'] == NULL)
            $this->add('callback_data', $text_keyboard['profile_birthday_unknown'], NULL, NULL, 1, 2);
        else
            $this->add('callback_data', $text_keyboard['profile_birthday'], NULL, NULL, 1, 2);

        $this->add('callback_data', $text_keyboard['main_close'], 'close', NULL, 2, 0);

        return $this->get();
    }
//
//    private function catalog_search ($TEXT_KEYBOARD): bool|string
//    {
//        $this->add('callback_data', $TEXT_KEYBOARD['search_catalog'], NULL, NULL, 0, 0);
//        $this->add('callback_data', $TEXT_KEYBOARD['search_brand'], NULL, NULL, 0, 1);
//        $this->add('callback_data', $TEXT_KEYBOARD['search_all'], NULL, NULL, 1, 0);
//        $this->add('callback_data', $TEXT_KEYBOARD['main_back'], NULL, NULL, 2, 0);
//
//        return $this->get();
//    }



//    private function catalog ($SQL_RESULT): bool|string
//    {
//
//        $sql_result = $SQL_RESULT->SELECT_FROM('*', 'category', NULL, 'count_characters');
//        $col = 0; $row = 0; $count = 0; $num_rows = 0;
//
//        foreach ($sql_result as $sql_value) {
//            $num_rows++;
//            if (iconv_strlen($sql_value['description']) <= 16) {
//                $count++;
//                $this->add('callback_data',
//                    $sql_value['description'] . ' [' . $sql_value['count_product'] . ']',
//                    'category', $sql_value['id'], $row, $col);
//                $col++;
//            } else {
//                if ($count >= 1) $row++;
//                $col = 0;
//                $this->add('callback_data',
//                    $sql_value['description'] . ' [' . $sql_value['count_product'] . ']',
//                    'category', $sql_value['id'], $row, $col);
//                $row++;
//            }
//            if ($col == 2) {
//                $count = 0;
//                $col = 0;
//                $row++;
//            }
//            if ($sql_result->num_rows == $num_rows)
//                $this->add('callback_data', 'Бренды', NULL, NULL, $row, 0);
//        }
//
//        return $this->get();
//    }
//
//    private function calendar (): bool|string
//    {
//        $v = 0;
//        for ($i = 1; $i <= 7; $i++) {
//            $v++;
//            $this->add('callback_data', $i, NULL, NULL, 0, $i-1);
//        }
//        for ($i = 1; $i <= 7; $i++) {
//            $v++;
//            $this->add('callback_data', $i+7, NULL, NULL, 1, $i-1);
//        }
//        for ($i = 1; $i <= 7; $i++) {
//            $v++;
//            $this->add('callback_data', $i+14, NULL, NULL, 2, $i-1);
//        }
//        for ($i = 1; $i <= 7; $i++) {
//            $v++;
//            $this->add('callback_data', $i+21, NULL, NULL, 3, $i-1);
//        }
//        for ($i = 1; $i <= 7; $i++) {
//            $v++;
//            if ($v <= 31)
//                $this->add('callback_data', $i+28, NULL, NULL, 4, $i-1);
//            else
//                $this->add('callback_data', '-', NULL, NULL, 4, $i-1);
//        }
////        $i = 0;
////        $this->add('callback_data', $i, NULL, NULL, 0, 0);
////        $this->add('callback_data', $i, NULL, NULL, 0, 0);
//        return $this->get();
//    }
}

class SQL
{
    private string $DB_database;
    private string $DB_hostname;
    private string $DB_username;
    private string $DB_password;
    private string $DB_keygen;

    private mysqli $DB_link;

    private string $TABLE_NAME;



    public function __construct ($DB_database, $DB_hostname, $DB_username, $DB_password, $DB_keygen)
    {
        $this->DB_database = $DB_database;
        $this->DB_hostname = $DB_hostname;
        $this->DB_username = $DB_username;
        $this->DB_password = $DB_password;
        $this->DB_keygen = $DB_keygen;

        $this->DB_link = new mysqli(
            hostname: $this->DB_hostname,
            username: $this->DB_username,
            password: $this->DB_password,
            database: $this->DB_database);

        $this->DB_link->query('SET CHARSET UTF8');
        $this->AUTO_CREATE();
    }

    private function AUTO_CREATE (): void
    {

        $this->CREATE_TABLE('users',
            "`id` int(11) NOT NULL PRIMARY KEY,
            `message_id` int(11) DEFAULT NULL,
            `callback_id` int(11) DEFAULT NULL,
            `username` varchar(255) DEFAULT NULL,
            `first_name` varchar(255) NOT NULL,
            `last_name` varchar(255) DEFAULT NULL,
            `profile_name` varchar(255) DEFAULT NULL,
            `phone_number` bigint(20) DEFAULT NULL,
            `birthday` date DEFAULT NULL,
            `sex` varchar(255) DEFAULT NULL,
            `address` varchar(255) DEFAULT NULL,
            `favorite` longtext,
            `cart_product` mediumtext,
            `cart_date` date DEFAULT NULL,
            `role` varchar(255) NOT NULL DEFAULT 'viewer'");

        $this->CREATE_TABLE('category',
            "`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `category` varchar(50) NOT NULL,
            `count_product` int(11) DEFAULT NULL,
            `count_characters` int(11) DEFAULT NULL,
            UNIQUE KEY `category_UNIQUE` (`category`),
            INDEX `category` (`category`)");

        $this->CREATE_TABLE('brand',
            "`id` INT (11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `brand` varchar(50) NOT NULL,
            `count_product` int(11) DEFAULT NULL,
            `count_characters` int(11) DEFAULT NULL,
            UNIQUE KEY `brand_UNIQUE` (`brand`),
            INDEX `brand` (`brand`)");

        $this->CREATE_TABLE('title',
            "`id` INT (11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `title` VARCHAR (150) NOT NULL,
            INDEX `title` (`title`)");

        $this->CREATE_TABLE('caption',
            "`id` INT (11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `caption` VARCHAR (1000) NOT NULL,
            INDEX `title` (`caption`)");

        $this->CREATE_TABLE('country',
            "`id` INT (11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `country` VARCHAR (50) NOT NULL,
            INDEX `title` (`country`)");

        $this->CREATE_TABLE('product',
            "`vendor_code` bigint(20) NOT NULL PRIMARY KEY,
            `title_id` INT (11) NOT NULL,
            `caption_id` INT (11) NOT NULL,
            `color` varchar(50) DEFAULT NULL,
            `country_id` INT (11) DEFAULT NULL,
            `price_old` int(11) DEFAULT NULL,
            `price_new` int(11) DEFAULT NULL,
            `image_id` varchar(150) DEFAULT NULL,
            `category_id` int(11) DEFAULT NULL,
            `brand_id` int(11) DEFAULT NULL,
            `group_id` int(11) DEFAULT NULL,
            `is_status` BOOLEAN DEFAULT TRUE,
            FOREIGN KEY brand (brand_id) REFERENCES _brand_m205r1G6NHNs (id),
            FOREIGN KEY category (category_id) REFERENCES _category_m205r1G6NHNs (id),
            FOREIGN KEY title (title_id) REFERENCES _title_m205r1G6NHNs (id),
            FOREIGN KEY caption (caption_id) REFERENCES _caption_m205r1G6NHNs (id)");

    }

    private function SHOW_TABLES (): bool|array|null
    {
        $result = $this->DB_link->query("SHOW TABLES FROM `$this->DB_database` LIKE '$this->TABLE_NAME'");
        return $result->fetch_array();
    }

    public function link (): mysqli
    {
        return $this->DB_link;
    }

    public function CREATE_TABLE ($TABLE_NAME, $PARAMS): void
    {
        $this->TABLE_NAME = $TABLE_NAME . '_' . $this->DB_keygen;
        if (!$this->SHOW_TABLES()) {
            $this->DB_link->query("CREATE TABLE `$this->DB_database`.`$this->TABLE_NAME` 
            ($PARAMS) ENGINE=MyISAM DEFAULT CHARSET=utf8");
        }
    }

    public function INSERT_INTO ($TABLE_NAME, $COLUMN, $VALUE): void
    {
        $TABLE_NAME = $TABLE_NAME . '_' . $this->DB_keygen;
        $this->DB_link->query("INSERT INTO $TABLE_NAME ($COLUMN) VALUES ($VALUE)");

    }

    public function SELECT_FROM ($SELECT, $FROM, $WHERE, $ORDER_BY): bool|array|null
    {
        $TABLE_NAME = $FROM . '_' . $this->DB_keygen;
        if ($WHERE)
            return $this->DB_link->query("SELECT $SELECT FROM `$TABLE_NAME` WHERE $WHERE")->fetch_assoc();
        elseif ($ORDER_BY)
            return $this->DB_link->query("SELECT $SELECT FROM `$TABLE_NAME` ORDER BY $ORDER_BY")->fetch_assoc();
        else
            return $this->DB_link->query("SELECT $SELECT FROM `$TABLE_NAME`")->fetch_assoc();
    }

    public function UPDATE ($TABLE_NAME, $SET, $WHERE): bool|mysqli_result
    {
        $TABLE_NAME = $TABLE_NAME . '_' . $this->DB_keygen;
        return $this->DB_link->query("UPDATE $TABLE_NAME SET $SET WHERE $WHERE");
    }

    public function connect_close (): void
    {
        $this->DB_link->close();
    }
}
