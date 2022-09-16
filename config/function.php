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
                'reply_markup' => $reply_markup
            );
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

//    public function deleteMessage ($chat_id, $message_id): void
//    {
//        $request_params = array(
//            'chat_id' => $chat_id,
//            'message_id' => $message_id
//        );
//        $this->curl(method: __FUNCTION__, request_params: $request_params);
//    }

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
        $this->keyboard = array($this->keyboard_type => array(), 'resize_keyboard' => true, 'one_time_keyboard' => $one_time_keyboard);
    }

    public function add ($keyboard_type, $text, $action, $type, $row, $coll): void
    {
            switch ($keyboard_type) {
                case 'request_contact':
                case 'request_location':
                    $button =
                        ["text" => $text,
                            $keyboard_type => $type];

                    $this->keyboard[$this->keyboard_type][$row][$coll] = $button;
                    break;

                case 'callback_data':
                    $button =
                        ["text" => $text,
                            $keyboard_type => "action:$action|type:$type"];

                    $this->keyboard[$this->keyboard_type][$row][$coll] = $button;
                    break;

                default:
                    $button =
                        ["text" => $text];

                    $this->keyboard[$this->keyboard_type][$row][$coll] = $button;
                    break;
            }
    }

    public function get (): bool|string
    {
        return json_encode($this->keyboard);
    }

    public function AUTO_CREATE ($AUTO, $TEXT_KEYBOARD, $SQL_RESULT): bool|string|null
    {
        $result = NULL;
        switch ($AUTO) {
            case 'main_menu':
                $result = $this->main_menu($TEXT_KEYBOARD, $SQL_RESULT);
                break;

            case 'calendar':
                $result = $this->calendar();
                break;

            case 'user_account':
                $result = $this->user_account($TEXT_KEYBOARD, $SQL_RESULT);
                break;
        }

        return $result;
    }

    private function main_menu ($TEXT_KEYBOARD, $SQL_RESULT): bool|string
    {
        $this->add(NULL, $TEXT_KEYBOARD['main_catalog'], NULL, NULL, 0, 0);

        $i = 0; $col = 0; $row = 1;

        if ($SQL_RESULT['cart_product']) {
            $i++;
            $this->add(NULL, $TEXT_KEYBOARD['main_cart'], NULL, NULL, $row, $col);
            $col++;
        }

        if ($SQL_RESULT['role'] == 'administrator') {
            $i++;
            $this->add(NULL, $TEXT_KEYBOARD['main_admin'], NULL, NULL, $row, $col);
            $col++;
        }

        if ($SQL_RESULT['favorite']) {
            $i++;
            $this->add(NULL, $TEXT_KEYBOARD['main_favorite'], NULL, NULL, $row, $col);
        }

        if ($i != 0) $row++;

        if ($SQL_RESULT['phone_number'])
           $this->add(NULL, $TEXT_KEYBOARD['main_profile'], NULL, NULL, $row, 0);
        else
            $this->add('request_contact', $TEXT_KEYBOARD['main_login'], NULL, true, $row, 0);

        $this->add(NULL, $TEXT_KEYBOARD['main_help'], NULL, NULL, $row, 1);
        return $this->get();
    }

    private function user_account ($TEXT_KEYBOARD, $SQL_RESULT): bool|string
    {
        $this->add(NULL, $TEXT_KEYBOARD['profile_history'], NULL, NULL, 0, 0);

        if ($SQL_RESULT['profile_name'] == NULL)
            $this->add(NULL, $TEXT_KEYBOARD['profile_name_unknown'], NULL, NULL, 1, 0);
        else
            $this->add(NULL, $TEXT_KEYBOARD['profile_name'], NULL, NULL, 1, 0);

        if ($SQL_RESULT['sex'] == NULL)
            $this->add(NULL, $TEXT_KEYBOARD['profile_sex_unknown'], NULL, NULL, 1, 1);
        else
            $this->add(NULL, $TEXT_KEYBOARD['profile_sex'], NULL, NULL, 1, 1);

        if ($SQL_RESULT['birthday'] == NULL)
            $this->add(NULL, $TEXT_KEYBOARD['profile_birthday_unknown'], NULL, NULL, 1, 2);
        else
            $this->add(NULL, $TEXT_KEYBOARD['profile_birthday'], NULL, NULL, 1, 2);

        $this->add(NULL, $TEXT_KEYBOARD['main_back'], NULL, NULL, 2, 0);

        return $this->get();
    }

    private function calendar (): bool|string
    {
        $v = 0;
        for ($i = 1; $i <= 7; $i++) {
            $v++;
            $this->add('callback_data', $i, NULL, NULL, 0, $i-1);
        }
        for ($i = 1; $i <= 7; $i++) {
            $v++;
            $this->add('callback_data', $i+7, NULL, NULL, 1, $i-1);
        }
        for ($i = 1; $i <= 7; $i++) {
            $v++;
            $this->add('callback_data', $i+14, NULL, NULL, 2, $i-1);
        }
        for ($i = 1; $i <= 7; $i++) {
            $v++;
            $this->add('callback_data', $i+21, NULL, NULL, 3, $i-1);
        }
        for ($i = 1; $i <= 7; $i++) {
            $v++;
            if ($v <= 31)
                $this->add('callback_data', $i+28, NULL, NULL, 4, $i-1);
            else
                $this->add('callback_data', '-', NULL, NULL, 4, $i-1);
        }
//        $i = 0;
//        $this->add('callback_data', $i, NULL, NULL, 0, 0);
//        $this->add('callback_data', $i, NULL, NULL, 0, 0);
        return $this->get();
    }
}

class SQL
{
    private string $DB_database;
    private string $DB_hostname;
    private string $DB_username;
    private string $DB_password;
    private string $DB_keygen;
    private string $DB_botname;

    private mysqli $DB_link;

    private string $TABLE_NAME;



    public function __construct ($DB_database, $DB_hostname, $DB_username, $DB_password, $DB_keygen, $DB_botname)
    {
        $this->DB_database = $DB_database;
        $this->DB_hostname = $DB_hostname;
        $this->DB_username = $DB_username;
        $this->DB_password = $DB_password;
        $this->DB_keygen = $DB_keygen;
        $this->DB_botname = $DB_botname;

        $this->DB_link = new mysqli(
            hostname: $this->DB_hostname,
            username: $this->DB_username,
            password: $this->DB_password,
            database: $this->DB_database);

        $this->AUTO_CREATE();
    }

    private function AUTO_CREATE (): void
    {
        $this->DB_link->query('SET CHARSET UTF8');

        $this->CREATE_TABLE('users',
            "`id` INT NOT NULL,
            `username` VARCHAR(255) NULL DEFAULT NULL,
            `first_name` VARCHAR(255) NOT NULL,
            `last_name` VARCHAR(255) NULL DEFAULT NULL,
            `profile_name` VARCHAR(255) NULL DEFAULT NULL,
            `phone_number` BIGINT NULL DEFAULT NULL,
            `language_code` VARCHAR(255) NULL DEFAULT NULL,
            `birthday` DATE NULL DEFAULT NULL,
            `sex` VARCHAR(255) NULL DEFAULT NULL,
            `address` VARCHAR(255) NULL DEFAULT NULL,
            `favorite` LONGTEXT NULL DEFAULT NULL,
            `cart_product` MEDIUMTEXT NULL DEFAULT NULL,
            `cart_date` DATE NULL DEFAULT NULL,
            `role` VARCHAR(255) NOT NULL DEFAULT 'viewer'", "`id`");

        $this->CREATE_TABLE('product',
            "`vendor_code` BIGINT NOT NULL,
            `barcode_original` BIGINT NULL DEFAULT NULL,
            `barcode_discount` BIGINT NULL DEFAULT NULL,
            `title` VARCHAR(255) NULL DEFAULT NULL,
            `caption` VARCHAR(255) NULL DEFAULT NULL,
            `color` VARCHAR(255) NULL DEFAULT NULL,
            `brand` VARCHAR(255) NULL DEFAULT NULL,
            `country` VARCHAR(255) NULL DEFAULT NULL,
            `category` INT NULL DEFAULT NULL,
            `price_old` INT NULL DEFAULT NULL,
            `price_new` INT NULL DEFAULT NULL,
            `image_id` INT NULL DEFAULT NULL,
            `creator` VARCHAR(255) NOT NULL", "`vendor_code`");

        $this->CREATE_TABLE('category',
            "`id` INT NOT NULL AUTO_INCREMENT,
            `count_product` INT NULL DEFAULT NULL,
            `description` VARCHAR(255) NOT NULL", "`id`");

//        $this->CREATE_TABLE('brand',
//            "`id` INT NOT NULL AUTO_INCREMENT,
//            `count_product` INT NULL DEFAULT NULL,
//            `description` VARCHAR(255) NOT NULL", "`id`");
    }

    private function SHOW_TABLES (): bool|array|null
    {
        $result = $this->DB_link->query("SHOW TABLES FROM `$this->DB_database` LIKE '$this->TABLE_NAME'");
        return $result->fetch_array();
    }

    public function CREATE_TABLE ($TABLE_NAME, $PARAMS, $PRIMARY_KEY): void
    {
        $this->TABLE_NAME = $this->DB_botname . '_' . $TABLE_NAME . '_' . $this->DB_keygen;
        if (!$this->SHOW_TABLES()) {
            $this->DB_link->query("CREATE TABLE `$this->DB_database`.`$this->TABLE_NAME` 
            ($PARAMS, PRIMARY KEY ($PRIMARY_KEY))");
        }
    }

    public function INSERT_INTO ($TABLE_NAME, $COLUMN, $VALUE): void
    {
        $TABLE_NAME = $this->DB_botname . '_' . $TABLE_NAME . '_' . $this->DB_keygen;
        $this->DB_link->query("INSERT INTO $TABLE_NAME ($COLUMN) VALUES ($VALUE)");

    }

    public function SELECT_FROM ($SELECT, $TABLE_NAME, $WHERE): bool|mysqli_result
    {
        $TABLE_NAME = $this->DB_botname . '_' . $TABLE_NAME . '_' . $this->DB_keygen;
        return $this->DB_link->query("SELECT $SELECT FROM `$TABLE_NAME` WHERE $WHERE");
    }

    public function UPDATE ($TABLE_NAME, $SET, $WHERE): bool|mysqli_result
    {
        $TABLE_NAME = $this->DB_botname . '_' . $TABLE_NAME . '_' . $this->DB_keygen;
        return $this->DB_link->query("UPDATE $TABLE_NAME SET $SET WHERE $WHERE");
    }

    public function connect_close (): void
    {
        $this->DB_link->close();
    }
}
