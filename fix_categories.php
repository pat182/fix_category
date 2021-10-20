<?php
//////test
require('../includes/configure.php');
ini_set('include_path', DIR_FS_CATALOG . PATH_SEPARATOR .
ini_get('include_path'));
chdir(DIR_FS_CATALOG);
require_once('includes/application_top.php');
$products = "SELECT c.parent_id,c.categories_id,pc.products_id,p.products_status FROM " .
TABLE_CATEGORIES  . " c 
Left JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " pc 
ON c.categories_id = pc.categories_id Left JOIN " . TABLE_PRODUCTS . " p on pc.products_id = p.products_id
WHERE c.categories_status = 1";
$products = $db->Execute($products);
$categories = [];
$removed = [];
$final = "";
function mySort($products,$removed){
        foreach ($products as $value) {
                if(($value["products_id"] === null || $value["products_id"] ===0 || $value["products_status"] == 0)){
                        $removed[$value["categories_id"]] = $value["categories_id"];
                        $categories[$value["categories_id"]] = $value;
                }
                else
                        $categories[$value["categories_id"]] = $value;
                $pos = array_search($value["parent_id"], $removed);
                if($pos == true){
                        unset($removed[$pos]);
                        mySort($categories[$pos]["parent_id"],$removed);
                }
        }
        $final = $removed;
        return implode(",",$final);
}
$d = mySort($products,$removed);
$updateQuery = "UPDATE " . TABLE_CATEGORIES . " SET categories_status = 0 WHERE categories_id in (". $d .")";
if($d!=""){
        $db->Execute($updateQuery);
        echo $db->affectedRows() . " categories updated: " . $d;
}else{
        echo "Nothing to edit";
}