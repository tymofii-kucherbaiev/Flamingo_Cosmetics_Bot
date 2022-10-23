<?php
/**
 * @var $input array
 * @var $core API
 */


//{
//    "update_id": 257238492,
//  "inline_query": {
//    "id": "1915089750632985840",
//    "from": {
//        "id": 445891579,
//      "is_bot": false,
//      "first_name": "Tymofii",
//      "last_name": "Kucherbaiev",
//      "username": "tymofii_kucherbaiev",
//      "language_code": "ru",
//      "is_premium": true
//    },
//    "chat_type": "sender",
//    "query": "1 123",
//    "offset": ""
//  }
//}


$result =
    [
        [
            "type" => "article",
            "id" => 1,
            "is_personal" => true,
            "title" => "Артикул: 4059729329264",
            "description" => "Водостойкий карандаш для глаз 20H Ultra Precision Gel 010",
            "thumb_url" => "https://i0.wp.com/dianomi-dn.com/wp-content/uploads/2022/08/5300e8f1-c982-11ec-80c9-9c8e99520657_7b8558a6-e7e3-11ec-80ca-9c8e99520657.jpeg",
            "reply_markup" => [
                'inline_keyboard' =>
                    [
                        [
                            [
                                'text' => 'English',
                                'callback_data' => '/lang_english'
                            ],
                            [
                                'text' => 'Русский',
                                'callback_data' => '/lang_russian'
                            ]
                        ],
                        [
                            [
                                'text' => 'Русский',
                                'callback_data' => '/lang_russian'
                            ]
                        ]
                    ]
            ],
            "message_text" => "message_text"
        ]
    ];

$core->answerInlineQuery($input['inline_query']['id'], $result);









