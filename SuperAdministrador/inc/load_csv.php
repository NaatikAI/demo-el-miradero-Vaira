<?php
function process_csv($file) {
 
    $file = fopen($file, "r");
    $data = array();
   
    while (!feof($file)) {
        $data[] = fgetcsv($file,null,';');
    }
   
    fclose($file);
    unset($data[0]);
    return $data;
   }
?>