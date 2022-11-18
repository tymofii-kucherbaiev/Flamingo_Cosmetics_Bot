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
            "type" => "photo",
            "id" => $key,
            "photo_url" => $value['image_id'],
            "thumb_url" => $value['image_id'],
//            "photo_width" => 480,
//            "photo_height" => 480,
            "title" => "Артикул: {$value['vendor_code']}",
            "description" => "{$value['title']}",
            "caption" => "/{$value['vendor_code']}",
//            "is_personal" => true,
//            "photo_file_id" => 'AgACAgQAAxkDAAIQIGN3PNF_hvOm3N6OsxVy-hqkw4wBAALirjEbS-NtUyNCPWh9dMcmAQADAgADeAADKwQ',
            "reply_markup" => $keyboard->test()
//                'inline_keyboard' =>
//                    [
//                        [
//                            [
//                                'text' => 'English',
//                                'callback_data' => '/lang_english'
//                            ],
//                            [
//                                'text' => 'Русский',
//                                'callback_data' => '/lang_russian'
//                            ]
//                        ],
//                        [
//                            [
//                                'text' => 'Русский',
//                                'callback_data' => '/lang_russian'
//                            ]
//                        ]
//                    ]

        ];
    }
    $core->answerInlineQuery($input['inline_query']['id'], $result);
}

