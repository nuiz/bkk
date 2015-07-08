<?php
/**
 * Created by PhpStorm.
 * User: NUIZ
 * Date: 9/6/2558
 * Time: 11:51
 */

$pattern = '/(\/properties\/edit\/prop_id:)\d+(\/ref_no:)\w+/';
$subject = file_get_contents("pppp.html");

$matches = [];
preg_match_all($pattern, $subject, $matches);