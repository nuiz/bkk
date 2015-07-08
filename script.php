<?php
session_start();
/*
 * greatrevenueproperty@gmail.com
 * GR1qaz2wsx
 */
header('Content-Type: text/html; charset=utf-8');
include 'vendor/autoload.php';
use Goutte\Client;

$client = new Client();
$a = array();
$crawler = $client->request('GET', 'http://crm.bkkcitismart.com/');
$form = $crawler->selectButton('')->form();
//$crawler = $client->submit($form, array('data[User][user_name]' => "akekarirk_h", 'data[User][password]' => "waiwaiwai01"));
$crawler = $client->submit($form, array('data[User][user_name]' => "prapatsorn_k", 'data[User][password]' => "9999"));
$crawler = $client->request('GET',"http://crm.bkkcitismart.com/Auth");
$crawler = $client->request('GET', 'http://crm.bkkcitismart.com/Properties');
$crawler = $client->request('GET', 'http://crm.bkkcitismart.com/properties/index/page:'.$_GET['page']);

$regexBookMark = '/(\'\/bookmark_group\/bookmarkgrp_popup\/runningnumber:)\w+(\/prop_id:)\d+(\/user_id:)\d+\'/';
$regexEdit = '/(\'\/properties\/edit\/prop_id:)\d+(\/ref_no:)\w+\'/';
$regexView = '/(\'\/properties\/propertyviewProject\/prop_id:)\d+\'/';

$dirFile = "edit";

if($_GET["download_type"] == "edit"){
    $regex = $regexEdit;
    $dirFile = "edit";
}
else if($_GET["download_type"] == "view"){
    $regex = $regexView;
    $dirFile = "view";
}

$subject = $crawler->html();
$matches = [];
preg_match_all($regex, $subject, $matches);

foreach($matches[0] as $key=> $value){
    $url = 'http://crm.bkkcitismart.com'.trim($value, "'");
    $node = $client->request('GET', $url);
    $myfile = fopen($dirFile."/".$_GET['page']."_".$key.".html", "a") or die("Unable to open file!");
    $txt = $node->html();
    fwrite($myfile, $txt);
    fclose($myfile);
}

//$crawler->filter('.tblList')->each(function ($node) {
//
//    print $node->text()."\n";
//
//    $myfile = fopen("file/".$_GET['id'].".txt", "a") or die("Unable to open file!");
//    $txt = $node->text()."---------------------------------------------------------------------------------------------------------------------------------  \n".PHP_EOL;
//    fwrite($myfile, $txt);
//    fclose($myfile);
//});


$_GET['page']++;
$url= "http://localhost/bkk/script.php?".http_build_query($_GET);
if ($_GET['page'] < $_GET["totalpage"] ) {
    header("Refresh: 2; URL=$url");
    echo "waiting sync page ".$_GET["page"]."/".$_GET["totalpage"];
} else {
    echo "sync ok";
}

//header("Refresh: 15; URL=$url");
/*
for ($i=1;$i<5;$i++) {
    $crawler = $client->request('GET', 'http://crm.bkkcitismart.com/properties/index/page:'.$i);
    $crawler->filter('.tblList')->each(function ($node) {
        print $node->text()."\n";
        $myfile = fopen("file/data.txt", "a") or die("Unable to open file!");
        $txt = $node->text()."---------------------------------------------------------------------------------------------------------------------------------  \n".PHP_EOL;
        fwrite($myfile, $txt);
        fclose($myfile);
    });
}
*/


/*
$r = $crawler->filter('a[class="up-link"]')->each(function ($node) {
    $a[] = $node->attr( 'rel' );
    return $a;
});
foreach ($r as $value) {
    echo $client->request('GET',"http://www.prakardproperty.com/properties/updatedate/".$value[0])->text();
}
*/


?>

