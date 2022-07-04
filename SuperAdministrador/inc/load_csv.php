<?php
function process_csv($file) {
    
    $file = fopen($file, "r");
    $data = array();
    
    while (!feof($file)) {
        $data[] = fgetcsv($file,0,';');
       
    }
    
    /*$data_log = json_encode($data); 
    echo '########';
    echo $data_log;
    echo "########";*/

    fclose($file);
    unset($data[0]);
    return $data;
   }
//[["Categoria,Proveedor,Nombre,Costo,Precio,Servicio"],["Salsa,Salsero de salsas,Valentina,10,12,0"],["Salsa,Salsero de salsas,Verde,10,13,0"],["Salsa,Salsero de salsas,Roja,12,14,0"],["refresco,pepsi,Jarrito 1L,15,20,0"],["refresco,pepsi,Manzanita 2L,16,21,0"],[null],false
//[["Categoria","Proveedor","Nombre","Costo","Precio","Servicio"],["Salsa","Salsero de salsas","Valentina","10","12","0"],["Salsa","Salsero de salsas","Verde","10","13","0"],["Salsa","Salsero de salsas","Roja","12","14","0"],["refresco","pepsi","Jarrito 1L","15","20","0"],["refresco","pepsi","Manzanita 2L","16","21","0"],[null],false] 
?>
