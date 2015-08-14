<?php
/**
 * Created by PhpStorm.
 * User: NUIZ
 * Date: 1/7/2558
 * Time: 10:56
 */

set_time_limit(0);
ini_set('memory_limit', '-1');
require("vendor/autoload.php");

class PropertyType {
    private static $values = [
        "A"=> "Apartment",
        "M"=> "Commercial Space",
        "C"=> "Condominium",
        "F"=> "Factory/Warehouse",
        "O"=> "Home office",
        "H"=> "House",
        "L"=> "Land",
        "S"=> "Shophouse",
        "T"=> "Townhouse",
        "R"=> "Others"
    ];

    public static function getValues(){
        return self::$values;
    }

    public static function getValue($key){
        return isset(self::$values[$key])? self::$values[$key]: null;
    }
}

class EditPage {
    private $html;
    public function __construct($html){
        $this->html = $html;
    }

    public function getPropertyID(){
        $pos = strpos($this->html, "Property ID:");
        $html2 = substr($this->html, $pos + strlen("Property ID:"));
        $pos2 = strpos($html2, "</strong>");
        $value = substr($html2, 0, $pos2);

        unset($pos, $html2, $pos2);
        return trim($value);
    }

    public function getOwner(){
        $regex = <<<REGEX
/<input name="fname1" type="text" id="fname1" onclick="showCustDetail\('customer1','\/customer\/view','800','490'\)" style="cursor:pointer" readonly value="([^"]*)">/
REGEX;

        $math = [];
        preg_match_all($regex, $this->html, $math);
        $value = array_pop($math[1]);
        $value = $this->_convertUTF($value);
        unset($regex, $math);
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
        unset($regex);
        return $this->_getValFromRegex($regex);
    }

    public function getSize(){
        $regex = <<<REGEX
/<input type="text" id="data\[Property\]\[size\]" name="data\[Property\]\[size\]" value="([^"]*)" class="numbers number-format">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getSizeType(){
        $tag = <<<TAG
<select name="data[Property][size_unit]" id="data[Property][size_unit]">
TAG;
        return $this->_getValFromSelectTag($tag);
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
        $regex = <<<TAG
<select id="data[Property][prop_status]" name="data[Property][prop_status]" disabled class="white-box">
TAG;
        return $this->_getValFromSelectTag($regex);
    }

    public function getDesiredProfit(){
        $regex = <<<REGEX
/<input type="text" id="data\[Property\]\[desired_profit\]" name="data\[Property\]\[desired_profit\]" class="numbers number-format" value="([^"]*)" onchange="relativeSellPriceChange\(\);">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getPropertyIncVat(){
        $regex = <<<REGEX
/<input[^>]* id="data\[Property\]\[inc_vat\]"[^>]*>/
REGEX;
        return $this->_getChecked($regex);
    }

    public function getRequirementType(){
        $tag = <<<TAG
<select name="data[Property][req_type]" id="data[Property][req_type]" onchange="checkReqType('data[Property][req_type]','data[Property][with_tenant]','For Sale');">
TAG;
        return $this->_getValFromSelectTag($tag);
    }
    public function getWithTenant(){
        $regex = <<<REGEX
/<input[^>]* id="data\[Property\]\[with_tenant\]"[^>]*>/
REGEX;
        return $this->_getChecked($regex);
    }

    public function getTenantPricePerMonth(){
        $regex = <<<REGEX
/<input type="text" name="data\[Property\]\[tenant_price\]" id="data\[Property\]\[tenant_price\]" value="([^"]*)" class="numbers number-format">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getSellingPrice(){
        $regex = <<<REGEX
/<input id="data\[Property\]\[sell_price\]" name="data\[Property\]\[sell_price\]" type="text" value="([^"]*)" class="numbers number-format">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getComission(){
        $regex = <<<REGEX
/<input id="data\[Property\]\[commission\]" name="data\[Property\]\[commission\]" type="text" class="numbers number-format" value="([^"]*)" onchange="relativeSellPriceChange\(\);">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getCommissionUnit(){
        $tag = <<<TAG
<select name="data[Property][commission_type]" id="data[Property][commission_type]" class="short">
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getMarkupType(){
        $tag = <<<TAG
<select id="data[Property][markup_price_type]" name="data[Property][markup_price_type]">
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getMarkuPrice(){
        $regex = <<<REGEX
<input type="text" id="data\[Property\]\[markup_price\]" name="data\[Property\]\[markup_price\]" class="numbers number-format" value="([^"]*)" onchange="relativeSellPriceChange\(\);">
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getPropertyType(){
        $tag = <<<TAG
<select name="data[Property][prop_type1]" id="data[Property][prop_type1]"
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    // waiting test
    public function getSoldPrice(){
        $regex = <<<REGEX
/<input id="data\[Property\]\[sold_price\]" name="data\[Property\]\[sold_price\]" type="text" value="([^"]*)" class="numbers number-format">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getTransferringDate(){
        $regex = <<<REGEX
/<input type="text" id="data\[Property\]\[transferring_date\]" name="data\[Property\]\[transferring_date\]" value="([^"]*)" size="13" readonly>/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getBranch(){
        $tag = <<<TAG
<select name="tmpBranchId" id="tmpBranchId"
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getRentalPrice(){
        $regex = <<<REGEX
/<input type="text" id="data\[Property\]\[rentail_price\]" name="data\[Property\]\[rentail_price\]" value="([^"]*)" class="numbers number-format">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getRentedPrice(){
        $regex = <<<REGEX
/<input type="text" id="data\[Property\]\[rented_price\]" name="data\[Property\]\[rented_price\]" value="([^"]*)" class="numbers number-format">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getTransferStatus(){
        $tag = <<<TAG
<select id="data[Property][unit_transfer_status]" name="data[Property][unit_transfer_status]"
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getAdditionalAreaPrice(){
        $regex = <<<REGEX
/<input type="text" id="data\[Property\]\[additional_area_price\]" name="data\[Property\]\[additional_area_price\]" value="([^"]*)" class="left numbers number-format">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getAreaPrice(){
        $regex = <<<REGEX
/<input type="text" id="data\[Property\]\[price_per_sqm\]" name="data\[Property\]\[price_per_sqm\]" class="numbers" value="([^"]*)" readonly>/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getProject(){
        $regex = <<<REGEX
/<input[^>]* id="data\[Property\]\[projectN\]"[^>]*>/
REGEX;
        if($this->_getChecked($regex)){
            unset($regex);
            return "Non-AP";
        }

        $regex = <<<REGEX
/<input[^>]* id="data\[Property\]\[projectA\]"[^>]*>/
REGEX;
        if($this->_getChecked($regex)){
            unset($regex);
            return "AP";
        }

        $regex = <<<REGEX
/<input[^>]* id="data\[Property\]\[projectNP\]"[^>]*>/
REGEX;
        if($this->_getChecked($regex)){
            unset($regex);
            return "Non-project";
        }

        unset($regex);
        return "";
    }
    public function getProjectName(){
        $tag = <<<TAG
<select name="data[Property][project_id]" id="data[Property][project_id]"
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getAddressNo(){
        $regex = <<<REGEX
/<input type="text" name="data\[Property\]\[unit_no\]" id="data\[Property\]\[unit_no\]" value="([^"]*)">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getCommissionUnitNo(){
        $regex = <<<REGEX
/<input type="text" name="data\[Property\]\[unit_type\]" id="data\[Property\]\[unit_type\]" value="([^"]*)">/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getOwnership(){
        $tag = <<<TAG
<select name="data[Property][owner_ship]" id="data[Property][owner_ship]"
TAG;
        return $this->_getValFromSelectTag($tag);
    }

    public function getBedrooms(){
        $regex = <<<REGEX
/<input type="text" name="data\[Property\]\[bedrooms\]" id="data\[Property\]\[bedrooms\]" size="3" maxlength="3" class="number-only" value="([^"]*)">/
REGEX;
        return $this->_getValFromRegex($regex); 
    }

    public function getBathrooms(){
        $regex = <<<REGEX
/<input type="text" name="data\[Property\]\[bathroomms\]" id="data\[Property\]\[bathroomms\]" size="3" maxlength="3" class="number-only" value="([^"]*)">/
REGEX;
        return $this->_getValFromRegex($regex); 
    }

    public function getKeyLocation(){
        $tag = <<<TAG
<select id="data[Property][key_location]" name="data[Property][key_location]">
TAG;
        return $this->_getValFromSelectTag($tag);
    }

    public function getFloors(){
        $regex = <<<REGEX
/<input type="text" name="data\[Property\]\[floor_no\]" id="data\[Property\]\[floor_no\]" size="3" maxlength="3" class="number-only" value="([^"]*)">/
REGEX;
        return $this->_getValFromRegex($regex); 
    }

    public function getProvince(){
        $tag = <<<TAG
<select name="data[Property][province_id]" id="data[Property][province_id]">
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getHeadLine(){
        $regex = <<<REGEX
/<input type="text" name="data\[Property\]\[head_line\]" value="([^"]*)" style="width:314px">/
REGEX;
        return $this->_getValFromRegex($regex); 
    }

    public function getRoad(){
        $regex = <<<REGEX
/<input type="text" name="data\[Property\]\[street\]" id="data\[Property\]\[street\]" value="([^"]*)">/
REGEX;
        return $this->_getValFromRegex($regex); 
    }

    public function getDistrict(){
        $tag = <<<TAG
<select name="data[Property][district_id]" id="data[Property][district_id]">
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getPropertyHighlight(){
        $tag = <<<TAG
<select id="data[Property][prop_highlight]" name="data[Property][prop_highlight]">
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getSubDistrict(){
        $tag = <<<TAG
<select name="data[Property][sub_district_id]" id="data[Property][sub_district_id]">
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getDescription(){
        $regex = <<<REGEX
/<textarea name="data\[Property\]\[description\]" id="textarea" rows="5" style="width:300px">([^<]*)<\/textarea>/
REGEX;
        return $this->_getValFromRegex($regex); 
    }

    public function getPromotion(){
        $regex = <<<REGEX
/<textarea name="data\[Property\]\[promotion\]" id="data\[Property\]\[promotion\]" rows="5" style="width:300px">([^<]*)<\/textarea>/
REGEX;
        return $this->_getValFromRegex($regex); 
    }

    public function getHeadLineEng(){
        $regex = <<<REGEX
/<input type="text" name="data\[PropertyTexts\]\[head_line\]" style="width:300px;background-color:#FAFF91" value="([^"])">/
REGEX;
        return $this->_getValFromRegex($regex); 
    }

    public function getWebStatus(){
        $tag = <<<TAG
<select name="data[Property][is_onweb]" id="data[Property][is_onweb]">
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getFeatureUnit(){
        $tag = <<<TAG
<select name="data[Property][is_recommend]" id="data[Property][is_recommend]">
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getDescriptionEng(){
        $regex = <<<REGEX
/<textarea name="data\[PropertyTexts\]\[description\]" id="textarea" style="width:300px" rows="5">([^<]*)<\/textarea>/
REGEX;
        return $this->_getValFromRegex($regex); 
    }

    public function getShortCut(){
        $regex = <<<REGEX
/<textarea name="data\[Property\]\[short_cut\]" id="data\[Property\]\[short_cut\]" style="width:310px" rows="5">([^<]*)<\/textarea>/
REGEX;
        return $this->_getValFromRegex($regex); 
    }

    public function getSourceCat(){
        $tag = <<<TAG
<select name="data[Property][category_id]" id="data[Property][category_id]"
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getSource(){
        $tag = <<<TAG
<select name="data[Property][source_id]" id="data[Property][source_id]"
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getSourceList(){
        $tag = <<<TAG
<select name="data[Property][list_id]" id="data[Property][list_id]"
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getRemark(){
        $regex = <<<REGEX
/<textarea name="data\[Property\]\[source_remark\]" style="width:725px;" rows="5" disabled>([^<]*)<\/textarea>/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getComment(){
        $regex = <<<REGEX
/<textarea name="data\[PropertyComm\]\[comment\]" id="data\[PropertyComm\]\[comment\]" rows="5" style="width:725px;" disabled>([^<]*)<\/textarea>/
REGEX;
        return $this->_getValFromRegex($regex);
    }

    public function getTierLevel(){
        $tag = <<<TAG
<select name="data[Property][tier_id]" id="data[Property][tier_id]">
TAG;
        return $this->_getTextFromSelectTag($tag);
    }

    public function getUpdateDate(){
        $pos = strpos($this->html, '<table class="tblList" id="rsTable">');
        $html = substr($this->html, $pos);
        $pos = strpos($html, "</table>");
        $html = substr($html, 0, $pos);
        $pos = strpos($html, "<tbody>");
        $html = substr($html, $pos);
        $pos = strpos($html, "<td>");
        $html = substr($html, $pos + strlen("<td>"));
        $pos = strpos($html, "</td>");
        $value = substr($html, 0, $pos);

        unset($pos, $html);
        return trim($value);
    }

    public function getUpdateMessage(){
        $pos = strpos($this->html, '<table class="tblList" id="rsTable">');
        $html = substr($this->html, $pos);
        $pos = strpos($html, "</table>");
        $html = substr($html, 0, $pos);
        $pos = strpos($html, "<tbody>");
        $html = substr($html, $pos);
        $pos = strpos($html, "<td>");
        $pos = strpos($html, "<td>", $pos + strlen("<td>"));
        $html = substr($html, $pos + strlen("<td>"));
        $pos = strpos($html, "</td>");
        $value = substr($html, 0, $pos);

        unset($pos, $html);
        return trim($value);
    }

    // tool function
    public function _getValFromRegex($regex){
        $math = [];
        preg_match_all($regex, $this->html, $math);
        $math = end($math);
        $value = end($math);
        unset($math, $regex);
        return $this->_convertUTF($value);
    }

    public function _getValFromSelectTag($tag){
        $pos = strpos($this->html, $tag);
        $html2 = substr($this->html, $pos);
        $pos2 = strpos($html2, "</select>");
        $htmlSelect = substr($html2, 0, $pos2 + strlen("</select>"));

        unset($pos, $html2, $pos2);

        $dom = new DOMDocument();
        $dom->loadHTML($htmlSelect);

        unset($htmlSelect);

        $optionElements = $dom->getElementsByTagName('option');
        foreach($optionElements as $option){
            if ($option->hasAttribute('selected')){
                $value = $option->hasAttribute('value')? $option->getAttribute('value'): $option->nodeValue;
                unset($dom, $optionElements, $option);
                return $this->_convertUTF($value);
            }
            unset($option);
        }
        $option = $optionElements->item(0);
        if(!empty($option)){
            $value = $option->hasAttribute('value')? $option->getAttribute('value'): $option->nodeValue;
            unset($dom, $optionElements, $option);
            return $this->_convertUTF($value);
        }
        unset($dom, $optionElements, $option);
        return "";
    }

    public function _getTextFromSelectTag($tag){
        $pos = strpos($this->html, $tag);
        $html2 = substr($this->html, $pos);
        $pos2 = strpos($html2, "</select>");
        $htmlSelect = substr($html2, 0, $pos2 + strlen("</select>"));

        unset($pos, $html2, $pos2);

        $regex = <<<REGEX
/<option([^>]*)>([^<]*)<\/option>/
REGEX;
        $math = [];
        preg_match_all($regex, $htmlSelect, $math);
        $listOption = $math[0];

        unset($math);

        $regex = <<<REGEX
/<option([^>]*) selected([^>]*)>([^<]*)<\/option>/
REGEX;
        $regex2 = <<<REGEX
/>([^<]*)<\/option>/
REGEX;

        foreach($listOption as $value){
            $math = [];
            preg_match($regex, $value, $math);
            if(count($math) > 0){
                if($math[3] == "please select"){
                    return "";
                }
                return $this->_convertUTF($math[3]);
            }
        }

        if(count($listOption) > 0){
            $math = [];
            preg_match($regex, $listOption[0], $math);
            if(count($math) > 0){
                if($math[3] == "please select"){
                    return "";
                }
                return $this->_convertUTF($math[3]);
            }
        }
        return "";
    }

    public function _getChecked($regex){
        $math = [];
        preg_match_all($regex, $this->html, $math);
        $doc = new DOMDocument();
        $doc->loadHTML($math[0][0]);
        $input = $doc->getElementsByTagName("input")->item(0);
        $value = $input->hasAttribute("checked");
        unset($math, $doc, $input);
        return $value;
    }

    public function _convertUTF($value){
        if(!mb_check_encoding($value, 'UTF-8')){
            $value = iconv("TIS-620", "UTF-8", $value);
        }
        return $value;
    }
}

class Main {
    private $dir = "edit";
    public function run(){
        $files = scandir($this->dir);
        $files = array_slice($files, 2);
        $pages = new SplFixedArray(count($files));
        $count = count($files);
        for($i = 0; $i< $count; $i++){
            $value = $this->readFile($files[$i]);
            if($value){
                $pages[$i] = $value;
            }
            if($i%100 == 0) echo "read ".$i."\n";
        }
        $this->writeExcel($pages);
    }

    public function readFile($file){
        if(!preg_match('/^\d+_\d+\.html$/', $file)){
            return false;
        }
        $html = file_get_contents($this->dir.'/'.$file);
        $pos = strrpos($html, "<body");
        $html2 = substr($html, $pos);
        $editPage = new EditPage($html2);

        $page = [
            "property_id"=> $editPage->getPropertyID(),
            "owner"=> $editPage->getOwner(),
            "resale_type"=> $editPage->getResaleType(),
            "size"=> $editPage->getSize(),
            "size_type"=> $editPage->getSizeType(),
            "original_price"=> $editPage->getOriginalPrice(),
            "actual_size"=> $editPage->getActualSize(),
            "property_status"=> $editPage->getPropertyStatus(),
            "desired_profit"=> $editPage->getDesiredProfit(),
            "inc_7%_vat"=> $editPage->getPropertyIncVat(),
            "requirement_type"=> $editPage->getRequirementType(),
            "with_tenant"=> $editPage->getWithTenant(),
            "tenant_price_per_month"=> $editPage->getTenantPricePerMonth(),
            "sell_price"=> $editPage->getSellingPrice(),
            "commission"=> $editPage->getComission(),
            "commission_unit"=> $editPage->getCommissionUnit(),
            "markup_type"=> $editPage->getMarkupType(),
            "markup_price"=> $editPage->getMarkuPrice(),
            "property_type"=> $editPage->getPropertyType(),
            "sold_price"=> $editPage->getSoldPrice(),
            "transferring_date"=> $editPage->getTransferringDate(),
            "branch"=> $editPage->getBranch(),
            "rental_price"=> $editPage->getRentalPrice(),
            "rented_price"=> $editPage->getRentedPrice(),
            "transfer_status"=> $editPage->getTransferStatus(),
            "additional_area_price"=> $editPage->getAdditionalAreaPrice(),
            "area_price"=> $editPage->getAreaPrice(),
            "project"=> $editPage->getProject(),
            "project_name"=> $editPage->getProjectName(),
            "address_no"=> $editPage->getAddressNo(),
            "commission_unit_no"=> $editPage->getCommissionUnitNo(),
            "owner_ship"=> $editPage->getOwnership(),
            "bedrooms"=> $editPage->getBedrooms(),
            "bathrooms"=> $editPage->getBathrooms(),
            "key_location"=> $editPage->getKeyLocation(),
            "floors"=> $editPage->getFloors(),
            "province"=> $editPage->getProvince(),
            "head_line"=> $editPage->getHeadLine(),
            "road"=> $editPage->getRoad(),
            "district"=> $editPage->getDistrict(),
            "property_highlight"=> $editPage->getPropertyHighlight(),
            "sub_district"=> $editPage->getSubDistrict(),
            "description"=> $editPage->getDescription(),
            "promotion"=> $editPage->getPromotion(),
            "head_line_eng"=> $editPage->getHeadLineEng(),
            "web_status"=> $editPage->getWebStatus(),
            "feature_unit"=> $editPage->getFeatureUnit(),
            "description_eng"=> $editPage->getDescriptionEng(),
            "shortcut"=> $editPage->getShortCut(),
            "source_category"=> $editPage->getSourceCat(),
            "source"=> $editPage->getSource(),
            "source_list"=> $editPage->getSourceList(),
            "remark"=> $editPage->getRemark(),
            "comment"=> $editPage->getComment(),
            "update_date"=> $editPage->getUpdateDate(),
            "update_message"=> $editPage->getUpdateMessage(),
            "tier_level"=> $editPage->getTierLevel()
        ];

        unset($editPage, $html, $pos, $html2, $file);
        return $page;
    }

    public function writeExcel($pages){
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $keyCount = 0;
        foreach($pages[0] as $key => $value){
            $objPHPExcel->getActiveSheet()->SetCellValue(PHPExcel_Cell::stringFromColumnIndex($keyCount++).'1', $key);
        }
        $row = 1;
        foreach($pages as $key=> $page){
            $row++;
            $keyCount = 0;
            foreach($page as $key => $value){
                $objPHPExcel->getActiveSheet()->SetCellValue(PHPExcel_Cell::stringFromColumnIndex($keyCount++).$row, $value);
            }
        }

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('excel/excel.xlsx');
        echo "writeExcel success";
    }
}

$main = new Main();
$main->run();