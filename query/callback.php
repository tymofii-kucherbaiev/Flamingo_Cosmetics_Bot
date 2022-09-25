<?php
/**
 * @var $data
 * @var $SQL
 * @var $API
 * @var $user_id
 * @var $user_username
 * @var $user_first_name
 * @var $user_last_name
 * @var $text_keyboard
 * @var $text_message
 * @var $SQL_result
 */

$result = $SQL->link()->query("SELECT brand.description AS 'Brand',

(SELECT GROUP_CONCAT(category.description ORDER BY category.category_count ASC SEPARATOR ', ') 
FROM category, brand_category 
WHERE category.id = brand_category.category_id and brand.id = brand_category.brand_id) AS 'Category'

FROM brand_category
INNER JOIN brand ON (brand.id = brand_category.brand_id)
#LEFT JOIN category ON (category.id = brand_category.category_id)

GROUP BY brand.description
ORDER BY brand.brand_count ASC");

$keyboard = new Keyboard('inline_keyboard', false);
$keyboard = $keyboard->AUTO_CREATE('test_2', $data['data'], $result);


//$API->sendMessage($text_message['welcome'].$val[1], $user_id, null);
$API->editMessageText($text_message['welcome'], $user_id, $data['message']['message_id'],$keyboard);