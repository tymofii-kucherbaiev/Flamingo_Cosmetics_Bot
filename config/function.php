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

    public function setWebhook ($directory): bool|string
    {
        $request_params = array (
            'url' => $directory
        );
        return $this->curl(method: __FUNCTION__, request_params: $request_params);

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

}


class Keyboard
{
    private array $keyboard;
    private string $type;

    public function __construct($type_keyboard, $one_time_keyboard)
    {
        $this->type = $type_keyboard;
        $this->keyboard = array($this->type => array(), 'resize_keyboard' => true, 'one_time_keyboard' => $one_time_keyboard);
    }

    public function add ($text, $action, $type, $row, $coll)
    {
        $button =
            ["text" => $text,
                "callback_data" => "action:$action|type:$type"];

        $this->keyboard[$this->type][$row][$coll] = $button;
    }

    public function get ()
    {
        return json_encode($this->keyboard);
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
//        $this->CREATE_TABLE('config',
//            "`value` VARCHAR(255) NOT NULL,
//            `description` VARCHAR(255) NULL DEFAULT NULL,
//            `active` BOOLEAN NULL DEFAULT NULL", "`value` (11)");

        $this->CREATE_TABLE('users',
            "`id` INT NOT NULL,
            `username` VARCHAR(255) NULL DEFAULT NULL,
            `first_name` VARCHAR(255) NOT NULL,
            `last_name` VARCHAR(255) NULL DEFAULT NULL,
            `phone_number` INT NULL DEFAULT NULL,
            `language_code` VARCHAR(255) NULL DEFAULT NULL,
            `birthday` DATE NULL DEFAULT NULL,
            `sex` VARCHAR(255) NULL DEFAULT NULL,
            `address` VARCHAR(255) NULL DEFAULT NULL,
            `user_role` VARCHAR(255) NOT NULL DEFAULT 'viewer'", "`id`");

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

//        $this->CREATE_TABLE('category',
//            "`id` INT NOT NULL,
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

    public function UPDATE ($TABLE_NAME, $SET, $WHERE)
    {
        $TABLE_NAME = $this->DB_botname . '_' . $TABLE_NAME . '_' . $this->DB_keygen;
        return $this->DB_link->query("UPDATE $TABLE_NAME SET $SET WHERE $WHERE");
    }

    public function connect_close (): void
    {
        $this->DB_link->close();
    }
}
