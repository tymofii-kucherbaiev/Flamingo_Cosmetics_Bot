<?php
/**
 * @var $data
 * @var $SQL
 * @var $user_id
 * @var $user_username
 * @var $user_first_name
 * @var $user_last_name
 */

switch ($data['text']) {
    case '/start':
        if (!$SQL->SELECT_FROM('*', 'users', "id = $user_id")->num_rows)
            $SQL->INSERT_INTO('users', 'id, username, first_name, last_name, language_code',
                "'$user_id', '$user_username', '$user_first_name', '$user_last_name', '{$data['from']['language_code']}'");

        $oKeyboard = new Keyboard('inline_keyboard', true);
        $oKeyboard->add(' Каталог', 'a', 'a', 0, 0);


        if ($SQL->SELECT_FROM('*', 'users', "id = $user_id AND phone_number IS NOT NULL")->num_rows)
            $oKeyboard->add(' Кабинет', 'a', 'a', 0, 1);
        else
            $oKeyboard->add(' Войти', 'a', 'a', 0, 1);


        $oKeyboard->add(' Заказы', 'a', 'a', 1, 0);
        $oKeyboard->add(' Помощь', 'a', 'a', 1, 1);
//$oKeyboard->add(' English', 'a', 'a', 2, 0);
//$oKeyboard->add(' Deutsch', 'a', 'a', 3, 1);
        $keyboard = $oKeyboard->get();
        break;


}