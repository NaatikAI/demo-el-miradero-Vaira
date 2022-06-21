<?php

require "../services/funciones.php";

if(isset($_POST['generaFactura']))
echo ('
  <script>
    alertFactura()
  </script>
');

$dias_semana = ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'];
$meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

// * ========================================================================================================
// *                                            PRODUCTO                                                   //
// * ========================================================================================================

if(isset($_POST['productsFilter'])){
  $data = [
    'fkUsuario' => $id_usuario,
    'rango' => 4,
    'fecha' => date('Y-m-d'),
    'fkSucursal' => $_SESSION['id_sucursal']
  ];

  $data_from_db = json_decode(POST('SuperAdministrador/services/getReportsProducts.php', $data), true);
  $products = json_decode($data_from_db[0], true);

  // var_dump($products);

  $total_sales = 0;
  $porcentaje_productos = [];
  $ventas = [];
  $array_products = [];

  foreach($products as $sale){
    $datos = (array)$sale;
    foreach($datos as $data){
      // ! Obtenemos el nombre de la categoria
      $array_products[] = $data['nombre'];
      foreach($data as $info){
        // ! Obtenemos el total de ventas de esa categoria
        if(gettype($info) == 'array')
          $ventas[] = $info['Ventas'];
      }
    }
  }

  // ! Se obtiene el total de ventas de la semana
  foreach ($ventas as $venta)
    $total_sales += $venta;

  $datos_grafica_categorias = [];
  if ($total_sales > 0){
    foreach ($ventas as $venta){
      $porcentaje_productos[] = round( $venta/$total_sales, 2);
    }
  }else{
    foreach ($ventas as $venta){
      $porcentaje_productos[] = 0;
    }
  }

  $data_for_p_graph = [];

  for($i = 0; $i < count($array_products); $i++){
    $data_for_p_graph[] = [
      'categoria' => $array_products[$i],
      'ventas' => $ventas[$i],
      'porcentaje' => $porcentaje_productos[$i]
    ];
  }
}

// * ========================================================================================================
// *                                            CATEGORIA                                                  //
// * ========================================================================================================

if(isset($_POST['categoriesFilter'])){
  // $data = [
  //   'fkUsuario' => $id_usuario,
  //   'rango' => 4,
  //   'fecha' => '2022-05-28'
  // ];

  $data = [
    'fkUsuario' => $id_usuario,
    'rango' => 4,
    'fecha' => date('Y-m-d')
  ];
  $data_from_db = json_decode(POST('Vendedor/services/getReportsCategories.php', $data), true);
  $categories = json_decode($data_from_db[0], true);
  
  $total_sales = 0;
  $porcentaje_categoria = [];
  $ventas = [];
  $array_categories = [];

  foreach($categories as $sale){
    $datos = (array)$sale;
    foreach($datos as $data){
      // ! Obtenemos el nombre de la categoria
      $array_categories[] = $data['nombre'];
      foreach($data as $info){
        // ! Obtenemos el total de ventas de esa categoria
        if(gettype($info) == 'array')
          $ventas[] = $info['Ventas'];
      }
    }
  }

  // ! Se obtiene el total de ventas generadas en total
  foreach ($ventas as $venta)
    $total_sales += $venta;

  $datos_grafica_categorias = [];
  if ($total_sales > 0){
    foreach ($ventas as $venta){
      $porcentaje_categoria[] = round( $venta/$total_sales, 2);
    }
  }else{
    foreach ($ventas as $venta){
      $porcentaje_categoria[] = 0;
    }
  }

  // ! Se crea el array que se enviara a la grafica
  $data_for_c_graph = [];
  for($i = 0; $i < count($array_categories); $i++){
    $data_for_c_graph[] = [
      'categoria' => $array_categories[$i],
      'ventas' => $ventas[$i],
      'porcentaje' => $porcentaje_categoria[$i]
    ];
  }
}

// * ========================================================================================================
// *                                            BUSQUEDA                                                   //
// * ========================================================================================================

if(isset($_POST['eligeFechaReporte']) && $_POST['eligeFechaReporte'] != ""){
  $data = [
    'fkUsuario' => $id_usuario,
    'fkSucursal' => $sucursal,
    'fecha' => $_POST['eligeFechaReporte'],
    'rango' => 1
  ];
  $input_from_db = json_decode(POST('Vendedor/services/filterSalesDate.php', $data), true);
  $total_dia = count($input_from_db);
  $fecha = $_POST['eligeFechaReporte'];
  $_dia = date('w', strtotime($fecha));
  $_mes = date('n', strtotime($fecha)) - 1;
  
  $fecha_parseada = $dias_semana[$_dia] . " " . date('d', strtotime($fecha)) . " " . $meses[$_mes] . " " . date('Y', strtotime($fecha));
}

// * ========================================================================================================
// *                                            SEMANAL                                                    //
// * ========================================================================================================

// ! FOR EXAMPLE
// $data = [
//   'fkUsuario' => $id_usuario,
//   'fecha' => '2022-05-22'
// ];

// ! FOR PRODUCTION
$data = [
  'fkUsuario' => $id_usuario,
  'fecha' => date('Y-m-d'),
  'fkSucursal' => $_SESSION['id_sucursal']
];

$input_week = json_decode(POST('Vendedor/services/getSalesWeek.php', $data), true);
$sales_week = json_decode($input_week[0]);

$total_week = 0;
$semana = [];


foreach($sales_week as $sale){
  $datos = (array)$sale;
  foreach($datos as $data){
    if(gettype($data) == 'object'){
      foreach($data as $dato){
        $ventas = (array)$dato;
        $semana[] = $ventas['Ventas'];
        
      } 
    } else
      $semana[] = 0;
  }
}


// // ! Se obtiene el total de ventas de la semana por dia
// foreach ($sales_week as $sale){
//   foreach ($sale as $sale_detail){
//     if(gettype($sale_detail) == 'array'){
//       $semana[] = count($sale_detail);
//     } else 
//       $semana[] = 0;
//   }
// }

// ! Se obtiene el total de ventas de la semana
foreach ($semana as $dia)
  $total_week += $dia;

$datos_grafica_semana = [];
if ($total_week > 0){
  foreach ($semana as $dia){
    $datos_grafica_semana[] = round( $dia/$total_week, 2);
  }
}else{
  foreach ($semana as $dia){
    $datos_grafica_semana[] = 0;
  }
}

$data_for_w_graph = [];
for($i = 0; $i < count($dias_semana); $i++){
  $data_for_w_graph[] = [
    'dia' => $dias_semana[$i],
    'ventas' => $semana[$i],
    'porcentaje' => $datos_grafica_semana[$i]
  ];
}

// * ========================================================================================================
// *                                           MENSUAL                                                     //
// * ========================================================================================================

$data = [
  'fkUsuario' => $id_usuario,
  'fkSucursal' => $_SESSION['id_sucursal']
];

$input_month = json_decode(POST('Vendedor/services/getSalesMonth.php', $data), true);
$sales_month = json_decode($input_month[0]);

$total_year = 0;
$mes = [];


// ! Se obtiene el total de ventas del año por mes
foreach($sales_month as $sale){
  $datos = (array)$sale;
  foreach($datos as $data){
    if(gettype($data) == 'object'){
      foreach($data as $dato){
        $ventas = (array)$dato;
        $mes[] = $ventas['Ventas'];
        
      } 
    } else
      $mes[] = 0;
  }
}

// // ! Se obtiene el total de ventas del año por mes
// foreach ($sales_month as $sale)
//   foreach ($sale as $sale_detail){
//     if(gettype($sale_detail) == 'array')
//       $mes[] = count($sale_detail);
//     else 
//       $mes[] = 0;
//   }

// ! Se obtiene el total de ventas del año
foreach ($mes as $m)
  $total_year += $m;

$datos_grafica_mes = [];
if ($total_year > 0)
  foreach ($mes as $m)
    $datos_grafica_mes[] = round( $m/$total_year, 2);
else
  foreach ($mes as $m)
    $datos_grafica_mes[] = 0;

$data_for_m_graph = [];
for($i = 0; $i < count($meses); $i++){
  $data_for_m_graph[] = [
    'mes' => $meses[$i],
    'ventas' => $mes[$i],
    'porcentaje' => $datos_grafica_mes[$i]
  ];
}
?>
<div class="row" style="margin-top: 5px;font-size: 19px;">
  <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="?recibos=true">Recibos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Reportes</li>
  </ol>
  </nav>
</div>
<div class="row">
  <div class="col" style="font-size: 20px;  margin-top: 10px;">
  <?php if (isset($_POST['eligeFechaReporte']) && $_POST['eligeFechaReporte'] != ''):  ?>
    <p>
      Resumen de <?php echo $_SESSION['nombre_sucursal'] . " del " . ($fecha_parseada) ?>
      <div class="row" style="margin-top: 5px;">
        <div class="col-4" style="border-style: none; text-align: left;">
          <a href="?reportes=true" class="btn btn-primary ms-3">
            <i class="fa fa-arrow-left"></i>
          </a>
        </div>
      </div>
    </p>
  <?php elseif (isset($_POST['productsFilter'])):  ?>
    <p>
      Ventas por productos anual de <?php echo $_SESSION['nombre_sucursal'] ?>
      <div class="row" style="margin-top: 5px;">
        <div class="col-4" style="border-style: none; text-align: left;">
          <a href="?reportes=true" class="btn btn-primary ms-3">
            <i class="fa fa-arrow-left"></i>
          </a>
        </div>
      </div>
    </p>
  <?php elseif (isset($_POST['categoriesFilter'])):  ?>
    <p>
      Ventas por categorias anual de <?php echo $_SESSION['nombre_sucursal'] ?>
      <div class="row" style="margin-top: 5px;">
        <div class="col-4" style="border-style: none; text-align: left;">
          <a href="?reportes=true" class="btn btn-primary ms-3">
            <i class="fa fa-arrow-left"></i>
          </a>
        </div>
      </div>
    </p>
  <?php else: ?>
    Resumen de <?php echo $_SESSION['nombre_sucursal'] ?>
    <form action="<?php echo( htmlspecialchars($_SERVER["PHP_SELF"]) ).'?reportes=true' ?>" method="POST" style="display:inline; margin-right: 27px; float:right;">
      <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          Sucursal 
        </button>
        <ul class="dropdown-menu">
          <?php
            foreach ($sucursales as $sucursal) {
              echo '<li><button type="submit" class="dropdown-item" name="idSucursal" value="'.$sucursal[0].'">'.$sucursal[1].'</button></li>';
            }
          ?>
        </ul>
      </div>
    </form>
    <div class="row" style="margin-top: 10px;">
      <div class="col-4" style="border-style: none; text-align: left;">
        <label class="toggle ms-3" id="status">
          <input type="checkbox" name="modo" id="modo">
          <span class="slider"></span>
          <!-- label element 👇 -->
          <span class="labels" data-on="Día" data-off="Mes"></span>
        </label>
      </div>
      <div class="col-5">
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']).'?reportes=true'?>" method="POST" style="display: inline;" style="margin-top:5">
          <button type="submit" class="btn btn-outline-dark" style="float: right; margin-left: 5px;" name="categoriesFilter">Ventas por categoría</button>
        </form>
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']).'?reportes=true'?>" method="POST" style="display: inline;" style="margin-top:5">
          <button type="submit" class="btn btn-outline-dark" style="float: right; margin-left: 5px;" name="productsFilter">Ventas por producto</button>
        </form>
      </div>
      <div class="col-3">
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']).'?reportes=true'?>" method="POST" style="display: inline;" style="margin-top:5">
          <input type="date" id="eligeFechareporte" name="eligeFechareporte" style="margin-right: 10px;float:left">
          <button type="submit" class="btn btn-primary fa fa-search" style="padding: 5px 12px; margin-top:-.8%;"></button>
        </form>
      </div>
    </div>
  <?php endif; ?>
  </div>
</div>
<div class="row" style="margin-top:9px" id="graph">
<?php
    if(isset($_POST['productsFilter'])){
      echo('
        <script>
        const divGrafica = document.getElementById("graph");      
        divGrafica.innerHTML = 
        `
        <table id="animations-example-3" class="charts-css column show-labels data-spacing-5 show-primary-axis">
          <caption> Ventas por productos </caption>
          <thead>
            <tr>
              <th scope="col"> Producto </th> 
              <th scope="col"> Ventas por producto </th>
              </tr>
          </thead> 
          <tbody class="bodyGrafica">');
            foreach ($data_for_p_graph as $product) {
              echo('
                <tr>
                  <th scope="row">'. $product['categoria'] .'</th>
                  <td style="--size: '. $product['porcentaje'] .';">
                  <span class="data">'.$product['ventas'].'</span>
                  </td>
                </tr>');
            }
              echo('
              </tbody>
            </table>
            `
        </script>
      ');
    }else if(isset($_POST['categoriesFilter'])){
      echo('
        <script>
        const divGrafica = document.getElementById("graph");      
        divGrafica.innerHTML = 
        `
        <table id="animations-example-3" class="charts-css column show-labels data-spacing-5 show-primary-axis">
          <caption> Ventas por dia </caption>
          <thead>
            <tr>
              <th scope="col"> Mes </th> 
              <th scope="col"> Ventas por categoria </th>
              </tr>
          </thead> 
          <tbody class="bodyGrafica">');
            foreach ($data_for_c_graph as $category) {
              echo('
                <tr>
                  <th scope="row">'. $category['categoria'] .'</th>
                  <td style="--size: '. $category['porcentaje'] .';">
                  <span class="data">'.$category['ventas'].'</span>
                  </td>
                </tr>');
            }
              echo('
              </tbody>
            </table>
            `
        </script>
      ');
  } else if(isset($_POST['eligeFechaReporte']) && $_POST['eligeFechaReporte'] != ''){
    echo ('
      <script>
      const divGrafica = document.getElementById("graph");      
      divGrafica.innerHTML = 
          `
          <div class="col-3">
            <table id="animations-example-3" class="charts-css column show-labels data-spacing-5 show-primary-axis" style="max-width: 300px">
              <caption> Ventas por dia </caption>
              <thead>
                <tr>
                  <th scope="col"> Dia </th> 
                  <th scope="col"> Ventas </th>
                  </tr>
              </thead> 
              <tbody class="bodyGrafica">
                <tr>
                  <th scope="row">'. $fecha_parseada .'</th>');
                  if($total_dia == 0)
                    echo('<td style="--size: 0;">');
                  else
                    echo('<td style="--size: 1.0;">');
                  echo('<span class="data" style="padding-top: 200px; font-size:35px">'.$total_dia.'</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-9">
            <div class="wrapper"  style="height:50vh;">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">No.Venta</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Sucursal</th>
                    <th scope="col">Monto</th>
                    <th scope="col">Detalle</th>
                    <th scope="col">Factura</th>
                  </tr>
                </thead>
                <tbody>');
                  $data = [
                    'idUsuario' => $id_usuario,
                    'fecha' => $_POST['eligeFechaReporte']
                  ];
                
                  $input_from_db = json_decode(POST("Administrador/services/getSalesPerDate.php",$data), true);                
                  if ($input_from_db == null){
                    echo('
                      <tr>
                        <th scope="row"></th>
                        <td>No se encontraron recibos</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                      </tr>
                      ');
                  } else{
                    foreach($input_from_db as $value){
                      //In between the pair of dots is supposed to be the variable $value andin brackets the specific value retrieved from the db
                      // NOTE: each button below must have a 'name' or/and 'value' attribute added, the modal wont work if we dont pass anything specific from each item
                      echo('
                      <tr>
                        <th scope="row">'.$value[0].'</th>
                        <td>'.fecha($value[1]).'</td>
                        <td>'.$value[2].'</td>
                        <td>$'.$value[3].'</td>
                        <td><button type="button" class="btn btn-outline-dark" style="float: center; margin-left: 15px;" data-bs-toggle="modal" data-bs-target="#mostrarDetalle'.$value[0].'"><i class="fa fa-search-plus"></i></button></td>
                        <td><button type="button" class="btn btn-outline-dark" style="float: center; margin-left: 15px;" data-bs-toggle="modal" data-bs-target="#generaFactura'. $value[0] .'"><i class="fa fa-book"></i></button></td>
                      </tr>
                      ');
                  }
                }
                
                echo('</tbody>
              </table>
            </div>  
          </div>
          `
      </script>
    ');
  } else {
    echo('
      <script>
        var modo = document.getElementById("modo");
        console.log(modo.checked);
        const divGrafica = document.getElementById("graph");
        if(!modo.checked){            
          divGrafica.innerHTML = 
            `
            <table id="animations-example-3" class="charts-css column show-labels data-spacing-5 show-primary-axis">
              <caption> Animation Example #3 </caption>
              <thead>
                <tr>
                  <th scope="col"> Year </th> 
                  <th scope="col"> Sales </th>
                  </tr>
              </thead> 
              <tbody class="bodyGrafica">');
              foreach($data_for_m_graph as $data){
                echo('
                  <tr>
                    <th scope="row">'. $data['mes'] .'</th>
                    <td style="--size: '. $data['porcentaje'] .';">
                    <span class="data">'.$data['ventas'].'</span>
                    </td>
                  </tr>');
              }
              echo('
              </tbody>
            </table>
            `
        }
        modo.addEventListener("change", function() {
          if(modo.checked){
            divGrafica.innerHTML = 
            `  
            <table id="animations-example-3" class="charts-css column show-labels data-spacing-5 show-primary-axis">
            <caption> Animation Example #3 </caption>
            <thead>
              <tr>
                <th scope="col"> Week </th> 
                <th scope="col"> Sales </th>
                </tr>
              </thead> 
              <tbody class="bodyGrafica"> ');
              foreach($data_for_w_graph as $data){
                echo('
                  <tr>
                    <th scope="row">'. $data['dia'] .'</th>
                    <td style="--size: '. $data['porcentaje'] .';">
                    <span class="data">'.$data['ventas'].'</span>
                    </td>
                  </tr>');
              }
              echo ('
              </tbody>
            </table> `
          } else{
            divGrafica.innerHTML = 
            `
            <table id="animations-example-3" class="charts-css column show-labels data-spacing-5 show-primary-axis">
              <caption> Animation Example #3 </caption>
              <thead>
                <tr>
                  <th scope="col"> Year </th> 
                  <th scope="col"> Sales </th>
                  </tr>
              </thead> 
              <tbody class="bodyGrafica">');
              foreach($data_for_m_graph as $data){
                echo('
                  <tr>
                    <th scope="row">'. $data['mes'] .'</th>
                    <td style="--size: '. $data['porcentaje'] .';">
                    <span class="data">'.$data['ventas'].'</span>
                    </td>
                  </tr>');
              }
              echo('</tbody>
            </table>
            `
          }
        });
      </script>
    ');
  }
?>
<!-- Modal Detalle Venta -->
<?php
if(isset($_POST['eligeFechaReporte'])){
  $data = [
    'idUsuario' => $id_usuario,
    'fecha' => $_POST['eligeFechaReporte']
  ];

  $input_from_db = json_decode(POST("Administrador/services/getSalesPerDate.php",$data), true);
  foreach($input_from_db as $value){

    $data = [
      'idVenta' => $value[0]
    ];
    $infoSale = json_decode(POST("Administrador/services/getInfoSale.php",$data), true);
    echo('
    <div class="modal fade bd-example-modal-xl" id="mostrarDetalle'.$value[0].'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Detalle de Venta</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form>
              <div class="mb-3">
                <label for="vendedor" class="col-form-label">Vendedor</label>
                <input type="text" class="form-control" id="vendedor" value="'.$infoSale[0][0].'" disabled>
              </div>
              <div class="mb-3">
                <label for="hora" class="col-form-label">Hora</label>
                <input type="text" class="form-control" id="hora" value="'.hora($infoSale[0][1]).'" disabled>
              </div>
              <div class="mb-3">
                <label for="productos" class="col-form-label">Productos</label>
                ');
                
                foreach($infoSale as $sale){
                  echo ('
                  <ul>
                    <li style="list-style:none;">'
                      .$sale[3] . " " . $sale[2] . '
                    </li>
                  </ul>');
                }
                
                echo('
              </div>
              <div class="mb-3">
                <label for="total" class="col-form-label">Total</label>
                <input type="text" class="form-control" id="total" value="'. $infoSale[0][5] .'" disabled>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>');

    echo('        
    <!-- Modal Factura-->
    <div class="modal fade bd-example-modal-xl" id="generaFactura'. $value[0] .'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Generar Factura</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- //* =========================================================================================== -->
            <!-- //*                 REALIZAR CONEXION CON EL SAT PARA REALIZAR UNA FACTURACION                  -->
            <!-- //* =========================================================================================== -->
            <!-- //*               MODIFICAR LAS PROPIEDADES DEL FORM PARA ESTABLECER LA CONEXION                -->
            <!-- //* =========================================================================================== -->
            <form action="'. htmlspecialchars($_SERVER['PHP_SELF']).'?reportes=true" method="POST" style="display: inline;">
              <div class="mb-3">
                <label for="rfc" class="col-form-label">Capture su RFC:</label>
                <input type="text" class="form-control" id="rfc" maxlength="13" required>
              </div>
              <div class="mb-3">
                <label for="nombre" class="col-form-label">Nombre Completo:</label>
                <input type="text" class="form-control" id="nombre" required>
              </div>
              <div class="mb-3">
                <label for="codigopostal" class="col-form-label">C&oacute;digo Postal:</label>
                <input type="text" class="form-control" id="codigopostal" maxlength="5" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" required>
              </div>
              <div class="mb-3">
                <label for="regimenfiscal" class="col-form-label">Régimen Fiscal:</label>
                <select name="regimenFiscal" id="regimenFiscal" form="carform">');
                    $input_from_db = json_decode(POST("Administrador/services/getTaxRegimen.php",$data), true);

                    foreach($input_from_db as $value){
                      echo('
                      <option value="'.$value[0].'">'.$value[1].'</option>
                      ');
                    }
                echo( '</select>
                <div class="mb-3">
                  <label for="metodopago" class="col-form-label">M&eacute;todo de Pago:</label>
                  <select name="cars" id="cars" form="carform">');
                    $input_from_db = json_decode(POST("Administrador/services/getPaymentMethods.php",$data), true);

                    foreach($input_from_db as $value){
                      echo('
                      <option value="'.$value[0].'">'.$value[1].'</option>
                      ');
                    }
                    echo('
                  </select>
                </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" name="generaFactura" class="btn btn-success">Generar</button>
            </form>
          </div>
        </div>
      </div>
    </div>        
  ');

  }      
}
?>

</div>

