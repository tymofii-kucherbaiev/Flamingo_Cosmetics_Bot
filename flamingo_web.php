<?php
/**
 * @var $core API
 */
require './config/function.php';


$sql_hostname = 'mn469049.mysql.tools';
$sql_database = 'mn469049_db';
$sql_username = 'mn469049_db';
$sql_password = 'jPWQQ8U9';

$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
);

$mysqli = new PDO("mysql:host=$sql_hostname;dbname=$sql_database", $sql_username, $sql_password, $options);
####################################################################################################################################
$urls = array(
    'https://dianomi-dn.com/product-category/catrice/karandashi-dlja-glaz-3/',
    '',
    '',
    '',
    ''
);
####################################################################################################################################
foreach ($urls as $url) {
    $url = $url . '?per_page=500';

    $get = file_get_contents($url);
    $get = explode('<div class="wd-sticky-loader"><span class="wd-loader"></span></div>', $get);
    $get = explode('<!-- .main-page-wrapper -->', $get[1]);

    preg_match_all('#<div class="product-element-top wd-quick-shop">\n\s<a href="(.*?)"#ms', $get[0], $preg, PREG_PATTERN_ORDER);

    foreach ($preg[1] as $value) {
        $product_get = file_get_contents($value);
        $result = explode('<div class="row product-image-summary-inner">', $product_get);
        $result = explode('<!-- .main-page-wrapper -->', $result[1]);

        preg_match('#<a data-elementor-open-lightbox="no" href="(.*?)\?.*class="product_title entry-title wd-entities-title">\s*(.*?)\s*([а-яА-Я].*?)\s*<.*<p class="price"><span class="woocommerce-Price-amount amount"><bdi>(.*?)&.*sku">\s*(.*?)\s*<.*?rel="tag">(.*?)<.*?class="wc-tab-inner">.*?<p>(.*?)<#sm',
            $result[0], $preg_product);

        $image = $preg_product[1];
        $brand = $preg_product[2];
        $title = str_replace('&#038;', '&', str_replace('&#8217;', "`", $preg_product[3]));
        $price = $preg_product[4];
        $vendor_code = $preg_product[5];
        $vendor_code_3 = substr($vendor_code, 0, 3);
        $category = $preg_product[6];
        $caption = str_replace('&#038;', '&', str_replace('&#8217;', "`", $preg_product[7]));

        $category_count = iconv_strlen($category);
        $brand_count = iconv_strlen($brand);


        $mysqli->query("CALL PC_product(
        $vendor_code,
        $price,
        '$title',
        '$category',
        $category_count,
        '$caption',
        '$brand',
        $brand_count,
        '$image',
        '$vendor_code_3');");
    }
}
echo '<h1>COMPLETE</h1> <h1>COMPLETE</h1> <h1>COMPLETE</h1>';
