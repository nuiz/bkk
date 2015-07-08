<?php
/**
 * Created by PhpStorm.
 * User: NUIZ
 * Date: 1/7/2558
 * Time: 10:56
 */

require("vendor/autoload.php");

class EditPage {
    private $html;
    public function __construct($html){
        $this->html = $html;
    }

    public function getOwner(){
        $regex = <<<REGEX
/<input name="fname1" type="text" id="fname1" onclick="showCustDetail\('customer1','\/customer\/view','800','490'\)" style="cursor:pointer" readonly value="([^"]*)">/
REGEX;

        $math = [];
        preg_match_all($regex, $this->html, $math);
        $value = array_pop($math[1]);
        $value = iconv("TIS-620", "UTF-8", $value);
        return $value;
    }

    public function getResaleType(){
        $regex = <<<REGEX
/<input type="text" name="data\[Property\]\[resale_type\]" id="data\[Property\]\[resale_type\]" value="([^"]*)" size="11" disabled>/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getContractPrice(){
        $regex = <<<REGEX
/<input type="text" id="data\[Property\]\[contract_price\]" name="data\[Property\]\[contract_price\]" class="numbers number-format" value="([^"]*)">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getSize(){
        $regex = <<<REGEX
/<input type="text" id="data\[Property\]\[size\]" name="data\[Property\]\[size\]" value="([^"]*)" class="numbers number-format">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getSizeType(){
        $regex = <<<REGEX
/<select name="data\[Property\]\[size_unit\]" id="data\[Property\]\[size_unit\]">(.|\n)*?<\/select>/
REGEX;
        return $this->_getVakFromRegexSelect($regex);
    }

    public function getOriginalPrice(){
        $regex = <<<REGEX
/<input type="text" id="data\[Property\]\[contract_price\]" name="data\[Property\]\[contract_price\]" class="numbers number-format" value="([^"]*)">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getActualSize(){
        $regex = <<<REGEX
/<input type="text" id="data\[Property\]\[actual_size\]" name="data\[Property\]\[actual_size\]" value="([^"]*)" class="numbers number-format">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getPropertyStatus(){
        $regex = <<<REGEX
/<select id="data\[Property\]\[prop_status\]" name="data\[Property\]\[prop_status\]" disabled class="white-box">(.|\n)*?<\/select>/
REGEX;
        return $this->_getVakFromRegexSelect($regex);
    }

    // tool function
    public function _getValFromRegex($regex){
        $math = [];
        preg_match_all($regex, $this->html, $math);
        $value = array_pop(array_pop($math));
        return $this->_convertUTF($value);
    }

    public function _getVakFromRegexSelect($regex){
        $math = [];
        preg_match_all($regex, $this->html, $math);
        $html = array_pop($math[0]);
        $regex = <<<REGEX
/<option(\s+[^>]*)?selected(\s+[^>]*)?>(.|\n)*?<\/option>/
REGEX;
        preg_match_all($regex, $html, $math);
        $optionHtml = $math[0][0];

        $regex = <<<REGEX
/value="([^"]*)"/
REGEX;
        preg_match_all($regex, $optionHtml, $math);
        return $this->_convertUTF($math[1][0]);
    }

    public function _convertUTF($value){
        return iconv("TIS-620", "UTF-8", $value);
    }
}

$editPage = new EditPage(file_get_contents('edit/4_0.html'));

$page = [
    "owner"=> $editPage->getOwner(),
    "resale_type"=> $editPage->getResaleType(),
    "size"=> $editPage->getSize(),
    "size_type"=> $editPage->getSizeType(),
    "original_price"=> $editPage->getOriginalPrice(),
    "actual_size"=> $editPage->getActualSize(),
    //"property_status"=> $editPage->getPropertyStatus(),

];

var_dump($page);
