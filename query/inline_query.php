<?php
/**
 * @var $input array
 * @var $core API
 * @var $mysqli mysqli_result
 * @var $keyboard keyboard
 */


$keyboard->keyboard_type = 'inline_keyboard';

if (iconv_strlen($input['inline_query']['query']) == 13) {
    $res = $mysqli->query("CALL PC_inline_mode({$input['inline_query']['query']})")->fetchAll();

    $count = count($res) - 1;

    $result = [];

    foreach ($res as $key => $value) {
        $result[] = [
            "type" => "article",
            "id" => $key,
            "thumb_url" => $value['image_id'],
            "title" => "Артикул: {$value['vendor_code']}",
            "description" => "{$value['title']}",
            "message_text" => "/{$value['vendor_code']}",
            "caption" => "/{$value['vendor_code']}"
        ];
    }
    $core->answerInlineQuery($input['inline_query']['id'], $result);
}

