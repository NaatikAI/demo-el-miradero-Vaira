<?php

require "../services/funciones.php";

if(isset($_POST['generaFactura']))
echo ('
  <script>
    alertFactura()
  </script>
');

?>

<div class="row" style="margin-top: 5px;font-size: 19px;">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page">Recibos</li>
    </ol>
    </nav>
</div>
              
<div style="font-size: 20px;  margin-top: 10px;">
  Recibos de <?php echo $_SESSION['nombre_sucursal'] ?>
  <div class="row" style="margin-top: 10px;">
    <div class="col">
      <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']).'?recibos=true'?>" method="POST" style="display: inline;">
        <input type="date" id="eligeFecha" name="eligeFecha">
        <button type="submit" class="btn btn-primary fa fa-search" style="padding: 5px 12px; margin-top:-.8%;"></button>
      </form>
    </div>
    <div class="col">
      <button type="button" class="btn btn-outline-dark" style="float: right; margin-left: 5px;" data-bs-toggle="modal" data-bs-target="#corteCaja">Hacer corte de caja</button>
      <form action="<?php echo( htmlspecialchars($_SERVER["PHP_SELF"]) ).'?recibos=true' ?>" method="POST" style="display:inline; float:right;">
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
    </div>
  </div>
  <div class="row-1" style="margin-top: 30px;">
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
      <tbody>
        <?php
          if (isset($_POST['eligeFecha']) && $_POST['eligeFecha'] != "") {
            $data = [
              'idSucursal' => $_SESSION['id_sucursal'],
              'fecha' => $_POST['eligeFecha']
            ];

            $input_from_db = json_decode(POST("SuperAdministrador/services/getSalesPerDate.php",$data), true);

          } else{ 
            $data = [
              'idSucursal' => $_SESSION['id_sucursal']
            ];
            $input_from_db = json_decode(POST("SuperAdministrador/services/getSales.php",$data), true);
          }

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
          }else{
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
                <td><button type="button" class="btn btn-outline-dark" style="float: center; margin-left: 15px;" data-bs-toggle="modal" data-bs-target="#generaFactura'.$value[0].'"><i class="fa fa-book"></i></button></td>
              </tr>
              ');
            }
          }
        ?>
      </tbody>
    </table>
    </div>
  </div>
</div>

<!-- Modal Detalle Venta -->
<?php
  $data = [
    'idSucursal' => $_SESSION['id_sucursal']
  ];
  
  $input_from_db = json_decode(POST("SuperAdministrador/services/getSales.php",$data), true);

  foreach($input_from_db as $value){
    $data = [
      'idVenta' => $value[0]
    ];
    $infoSale = json_decode(POST("SuperAdministrador/services/getInfoSale.php",$data), true);
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
            
            <form action="'. htmlspecialchars($_SERVER['PHP_SELF']).'?recibos=true" method="POST" style="display: inline;">
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
                    $input_from_db = json_decode(POST("Vendedor/services/getTaxRegimen.php",$data), true);

                    foreach($input_from_db as $value){
                      echo('
                      <option value="'.$value[0].'">'.$value[1].'</option>
                      ');
                    }
                echo( '</select>
                <div class="mb-3">
                  <label for="metodopago" class="col-form-label">M&eacute;todo de Pago:</label>
                  <select name="cars" id="cars" form="carform">');
                    $input_from_db = json_decode(POST("Vendedor/services/getPaymentMethods.php",$data), true);

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
            <button type="submit" name="generaFactura" class="btn btn-success" >Generar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  ');
    }
    
  
?>
   

<!-- Modal Corte de Caja -->
<div class="modal fade bd-example-modal-xl" id="corteCaja" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Corte de caja</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="vendedor" class="col-form-label">Selecciona el día en la que deseas hacer el corte de caja</label>
            <br>
            <input type="date" id="eligeFechaCorte" name="eligeFechaCorte">
          </div>
          <div class="mb-3">
            <label for="hora" class="col-form-label">Selecciona la sucursal</label>
            <br>
            <div class="btn-group">
              <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                  Sucursal
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#">Cuernavaca</a></li>
                  <li><a class="dropdown-item" href="#">Temixco</a></li>
                  <li><a class="dropdown-item" href="#">Xochitepec</a></li>
                </ul>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" onclick="alertGeneraDocCorteCaja()" data-bs-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>