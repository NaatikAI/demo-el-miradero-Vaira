<?php 
  require "../services/funciones.php";

  if(isset($_POST['generaFactura']))
    echo ('
      <script>
        alertFactura()
      </script>
    ');

    if(isset($_POST['generar_corte'])){
      $data = [
        "fkUsuario"=>$id_usuario,
        "fkSucursal"=>$sucursal,
        "fecha_inicio"=>$_POST['date_init'],
        "fecha_final"=>$_POST['date_final']
      ];
      $json_corte_caja = json_decode(Post("Vendedor/services/doCorteCaja.php",$data),true);
      var_dump($json_corte_caja);
      echo ('
          <script>
            alertGeneraDocCorteCajaAdmin()
          </script>
        ');
    }
  
    if(isset($_POST['generar_devolucion'])){
      $restaurar = 0;
      if(isset($_POST['restaurar']))
        $restaurar = 1;
  
      $data = [
        "fecha"=>$_POST['fechaDeCompra'],
        "idVenta"=>$_POST['noCompra'],
        "usuario"=>$_POST['user'],
        "password"=>$_POST['password'],
        "restaurar"=>$restaurar,
        "fkUsuario"=>$id_usuario,
        "fkSucursal"=>$sucursal
      ];
      $status_devolucion = json_decode(Post("Vendedor/services/doRefund.php",$data),true);
      if($status_devolucion[0] == 'Devolución autorizada')
        echo ('
          <script>
            alertDevolucion()
          </script>
        ');
    }
?>

<div class="row" style="margin-top: 5px;font-size: 19px;">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page">Recibos</li>
    </ol>
    </nav>
</div>              
<div style="font-size: 20px;  margin-top: 10px;">
    <div class="row" style="margin-top: 10px;">
      <div class="col">
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']).'?recibos=true'?>" method="POST" style="display: inline;">
          <input type="date" id="eligeFecha" name="eligeFecha">
          <button type="submit" class="btn btn-primary fa fa-search" style="padding: 5px 12px; margin-top:-.8%;"></button>
        </form>
      </div>
      <div class="col">
        <button type="button" class="btn btn-outline-dark" style="float: right; margin-left: 5px;" data-bs-toggle="modal" data-bs-target="#corteCaja">Hacer corte de caja</button>
        <button type="button" class="btn btn-outline-dark" style="float: right;" data-bs-toggle="modal" data-bs-target="#llaveAdmin">Hacer devoluci&oacute;n</button>
      </div>
    </div>
  <div class="row-1" style="margin-top: 30px;">
  <div class="wrapper"  style="height:55vh;">
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
          // Here starts a request to get data from the data base
          // the variable $input_from_db stores all data from database as list (if not make adjustments in foreach)
          // $input_from_db = array(
          //   "key1"=>"",
          //   "key2"=>"",
          //   "key3"=>"",
          //   "key4"=>"",
          //   "key5"=>"",
          // );
          
          if (isset($_POST['eligeFecha']) && $_POST['eligeFecha'] != "") {
            $data = [
              'idUsuario' => $id_usuario,
              'fecha' => $_POST['eligeFecha']
            ];
          
            $input_from_db = json_decode(POST("Administrador/services/getSalesPerDate.php",$data), true);
          
          } else{ 
            $data = [
              'idUsuario' => $id_usuario
            ];
            $input_from_db = json_decode(POST("Administrador/services/getSales.php",$data), true);
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
            'idUsuario' => $id_usuario
          ];
          
          $input_from_db = json_decode(POST("Administrador/services/getSales.php",$data), true);

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
<div class="modal fade" id="corteCaja" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']).'?recibos=true'?>" method="POST" style="display: inline;">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Corte de caja</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="d-flex justify-content-center">
                <div class="col-6 mb-4 me-3">
                  <div class="text-center">
                    <label for="">Fecha de Inicio</label>
                  </div>
                  <input type="datetime-local" id="date_init" name="date_init">
                </div>
                <div class="col-6">
                  <div class="text-center">
                    <label for="">Fecha de Fin</label>
                  </div>
                  <input type="datetime-local" id="date_final" name="date_final">
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
              <button type="submit" class="btn btn-success" name="generar_corte">Si</button>
              <!-- onclick="alertGeneraDocCorteCajaAdmin()" data-bs-dismiss="modal" -->
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- Modal llave administrador-->
    <div class="modal fade" id="llaveAdmin" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Ingresar claves de acceso de administrador</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']).'?recibos=true'?>" method="POST" style="display: inline;">
              <div class="form-group">
                  <label for="user"><i class="fa fa-fw fa-user"></i>Usuario</label>
                  <input id="user" type="text" class="form-control"  name="user" value="">
              </div>
              <div class="form-group">
                  <label for="password"><i class="fa fa-fw fa-key"></i>Contrase&ntilde;a</label>
                  <input id="password" type="password" class="form-control" name="password"  maxlength="16">
              </div>
              <div class="mt-3">
                  <label for="fechaCompra" class="col-form-label">Fecha de compra</label>
                  <input type="date" id="fechaDeCompra" name="fechaDeCompra">
              </div>
              <div class="">
                  <label for="noCompra" class="col-form-label">N&uacute;mero de compra</label>
                  <input type="text" id="noCompra" name="noCompra">
              </div>
              <div class="">
                <label for="restaurar" class="col-form-label">¿Desea regresar productos?</label>
                <input type="checkbox" id="restaurar" name="restaurar">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
              <button type="summit" class="btn btn-success" data-bs-toggle="modal" name="generar_devolucion">Hacer devolución</button>
              <!-- onclick="alertDevolucion()" data-bs-dismiss="modal" -->
            </div>
          </form>
        </div>
      </div>
    </div>