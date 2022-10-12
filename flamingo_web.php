<?php
require './config/function.php';
//$url = 'https://dianomi-dn.com/product/catrice-vodostojkij-karandash-dlja-glaz-20h-ultra-precision-gel-010';




$mysql_hostname = 'mn469049.mysql.tools';
$mysqli_username = 'mn469049_db';
$mysqli_password = 'jPWQQ8U9';
$mysqli_database = 'mn469049_db';

$mysql = new mysqli (hostname: $mysql_hostname, username: $mysqli_username, password: $mysqli_password, database: $mysqli_database);


//Replace the below connection parameters to fit your environment
$host = 'mn469049.mysql.tools';
$db = 'mn469049_db';
$user = 'mn469049_db';
$pass = 'jPWQQ8U9';

$cn=new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$cn->query('SET CHARSET UTF8');
//$q=$cn->exec('call product_list(4059729329462)');
//$res=$cn->query('call product_list(4059729329462)')->fetchAll();
$res = $cn->query("call select_user(445891579)")->fetch();

echo '<xmp>';
if ($res)
    var_dump($res);
    echo $res->id;
echo '</xmp>';
//$array = array(
//    'BIELITA',
//    'VITEX',
//    'CATRICE',
//    'EVELINE',
//    'ESSENCE',
//    'FAMILY COSMETIC',
//    'FFLEUR',
//    'GOLDEN ROSE',
//    'RUBY ROSE',
//    'RONNEY',
//    'OLIVIA VALERA'
//);
//
//
//foreach ($array as $value) {
//
//    $iconv = iconv_strlen($value);
//
//    $mysql->query('INSERT INTO brand (`brand`, `count_product`, `count_characters`) VALUES ('$value', 0, '$iconv')');
//}















//$urls = array("https://dianomi-dn.com/product/catrice-vodostojkij-karandash-dlja-glaz-20h-ultra-precision-gel-010/",
//    "https://dianomi-dn.com/product/catrice-vodostojkij-karandash-dlja-glaz-20h-ultra-precision-gel-020/",
//    "https://dianomi-dn.com/product/catrice-vodostojkij-karandash-dlja-glaz-20h-ultra-precision-gel-030/",
//    "https://dianomi-dn.com/product/catrice-vodostojkij-karandash-dlja-glaz-20h-ultra-precision-gel-040/",
//    "https://dianomi-dn.com/product/catrice-vodostojkij-karandash-dlja-glaz-20h-ultra-precision-gel-050/",
//    "https://dianomi-dn.com/product/catrice-vodostojkij-karandash-dlja-glaz-20h-ultra-precision-gel-060/",
//    "https://dianomi-dn.com/product/catrice-vodostojkij-karandash-dlja-glaz-20h-ultra-precision-gel-070/",
//    "https://dianomi-dn.com/product/catrice-vodostojkij-karandash-dlja-glaz-20h-ultra-precision-gel-080/");
//
//function get_web_page( $url )
//{
//    $ch = curl_init( $url );
//
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
//    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
//    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//
//    $content = curl_exec( $ch );
//    $header  = curl_getinfo( $ch );
//    curl_close( $ch );
//    $header['content'] = $content;
//
//    return $header;
//}
//
//$caption = 'Благодаря ультратонкому грифелю диаметром 2 мм автоматический карандаш Eye Pencil с усовершенствованной формулой обеспечивает сверхточное нанесение. Мягкая, водостойкая гелевая текстура обеспечивает высокую интенсивность цвета и стойкость до 20 часов, позволяя дополнить макияж глаз эффектными акцентами. Глубокий чёрный, теплый коричневый или насыщенный синий — карандаш представлен в универсальных оттенках, которые подойдут под любой цвет глаз.';

//foreach ($urls as $url) {
//    $code = get_web_page($url);
//
//    echo '<xmp>';
//    $res = explode('<div class="row product-image-summary-inner">', $code['content']);
//    $res = explode('</div><!-- .summary -->', $res[1]);
//    preg_match('#<a data-elementor-open-lightbox.*href="(.*);ssl=1">#', $res[0], $image);
//    preg_match('#<h1 class="product_title entry-title wd-entities-title">(.*)</h1>#sm', $res[0], $title);
//    preg_match('#<span class="sku">\s*([0-9]*)#sm', $res[0], $barcode);
//
//    $title[1] = str_replace("\n	", '', $title[1]);
//
////var_dump($title[1]);
//
//
////    $image = file_get_contents($image[1]);
////    file_put_contents($barcode[1] . '.webp', $image);
//
//
//    echo '</xmp>';
//
//    $DB_database = 'mn469049_db';
//    $DB_hostname = 'mn469049.mysql.tools';
//    $DB_username = 'mn469049_db';
//    $DB_password = 'jPWQQ8U9';
//    $DB_keygen = 'm205r1G6NHNs'; //12 values
//
//    $SQL = new SQL ($DB_database, $DB_hostname, $DB_username, $DB_password, $DB_keygen);
//    $SQL->INSERT_INTO('product', 'vendor_code, title, caption, price_old, brand_id, category_id',
//        "$barcode[1], '1', '1', 343, 1, 1");
//}
//file_put_contents('text.php', $res[0]);
