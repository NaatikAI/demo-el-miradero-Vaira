<?php

$data = [
  'sucursal' => $_SESSION['id_sucursal']
];

if(isset($_POST['addCategoria'])){
  $data = [
    'nombre' => $_POST['nombre'],
    'impuestoIVA' => $_POST['iva'],
    'descripcion' => $_POST['descripcion'],
  ];

  if($_POST['isr'] == '')
    $data['isr'] = 0.0;
  else
    $data['isr'] = $_POST['isr'];
    
  if($_POST['ieps'] == '')
    $data['ieps'] = 0.0;
  else
    $data['ieps'] = $_POST['ieps'];

  $status = json_decode(POST('SuperAdministrador/services/addCategorie.php', $data),true);
  if($status == 'Success')
    echo ('
      <script>
        $title = "Categoria agregada"
        $msg = "La categoria se ha agregado correctamente"
        alertSuccess($title, $msg)
      </script>
    ');

}

if(isset($_POST['addProduct'])){
  $check = @getimagesize($_FILES['img'.$_POST['idProducto']]['tmp_name']);
  if($check !== false){
    $data = [
      'nombre' => $_POST['nombre'],
      'costo' => $_POST['costo'],
      'precio' => $_POST['precio'],
      'imagen' => $_FILES['img']['name'],
      'categoria' => $_POST['categoria'],
      'proveedor' => $_POST['proveedor'],
      'activo' => 1,
      'servicio' => $_POST['servicio']
    ];
  } else {
    $data = [
      'nombre' => $_POST['nombre'],
      'costo' => $_POST['costo'],
      'precio' => $_POST['precio'],
      'imagen' => "",
      'categoria' => $_POST['categoria'],
      'proveedor' => $_POST['proveedor'],
      'activo' => 1,
      'servicio' => $_POST['servicio']
    ];
  }
            
  $status = json_decode(POST('SuperAdministrador/services/addProduct.php', $data), true);
  if($status == "Success"){
    if($check !== false){
      $carpeta_destino = '../src/image/productos/';
      $archivo_subido = $carpeta_destino . $_FILES['img']['name'];
      move_uploaded_file($_FILES['img']['tmp_name'], $archivo_subido);
    }
    echo '
    <script>
      alertAgregarProducto()
    </script>
    ';
  } else {
    echo '
    <script>
      $msg = "Error al agregar el producto";
      alertError($msg)
    </script>
    ';
  }
}

if(isset($_GET["id"])){
  $data = [
    'idProducto' => $_GET["id"]
  ];
  $eliminar = json_decode(POST("SuperAdministrador/services/deleteProduct.php",$data), true);
  if($eliminar != null)
    echo "<script>Swal.fire({
      title: 'Producto eliminado!',
      icon: 'success',
        confirmButtonText: 'Ok'
      }).then((result)=>{
        if(result.isConfirmed){
          window.location.href='index.php?inventario=true';
        }
      }) </script>";
  else
    echo "<script>Swal.fire({
      title: 'Error',
      text: 'No se pudo eliminar el producto',
      icon: 'error',
        confirmButtonText: 'Ok'
      }).then((result)=>{
        if(result.isConfirmed){
          window.location.href='index.php?inventario=true';
        }
      }) </script>";
}

if(isset($_POST['cargar_csv'])){
  require "load_csv.php";
  $carpeta_destino = "./temp_csv/";      
  $archivo_subido = $carpeta_destino . $_FILES['adjunto']['name'];
  move_uploaded_file($_FILES['adjunto']['tmp_name'], $archivo_subido);
  $resultado = process_csv($archivo_subido);
  foreach ($resultado as $linea){
    if($linea){
      $info_producto = explode(",", $linea[0]);
      if($info_producto[0] != ''){
        $data = [
          'categoria' => $info_producto[0],
          'proveedor' => $info_producto[1],
          'nombre'    => $info_producto[2],
          'costo'     => $info_producto[3],
          'precio'    => $info_producto[4],
          'servicio'  => $info_producto[5],
          'imagen'    => "",
          'activo'    => 1
        ];  
      }
      $status = json_decode(POST('SuperAdministrador/services/addMultipleProducts.php', $data), true);
    }
  }
  if($status[0] != '¡Error!')
    echo ('
      <script>
        $title = "Exito!"
        $msg = "Productos agregados correctamente"
        alertSuccess($msg)
      </script>
    ');

  unlink($archivo_subido);
}

if(isset($_POST['edit-product'])){
  $check = @getimagesize($_FILES['img'.$_POST['idProducto']]['tmp_name']);
  if($check !== false){
    $data = [
      'idProducto' => $_POST['idProducto'],
      'nombre' => $_POST['nombre'.$_POST['idProducto']],
      'costo' => $_POST['costo'.$_POST['idProducto']],
      'precio' => $_POST['precio'.$_POST['idProducto']],
      'img' => $_FILES['img'.$_POST['idProducto']]['name'],
      'categoria' => $_POST['categoria'.$_POST['idProducto']],
      'proveedor' => $_POST['proveedor'.$_POST['idProducto']],
    ];
  } else {
    $data = [
      'idProducto' => $_POST['idProducto'],
      'nombre' => $_POST['nombre'.$_POST['idProducto']],
      'costo' => $_POST['costo'.$_POST['idProducto']],
      'precio' => $_POST['precio'.$_POST['idProducto']],
      'img' => "",
      'categoria' => $_POST['categoria'.$_POST['idProducto']],
      'proveedor' => $_POST['proveedor'.$_POST['idProducto']],
    ];
  }
            
  $status = json_decode(POST('SuperAdministrador/services/updateProduct.php', $data), true);
  if($status[0] == "Success"){
    if($check !== false){
      $carpeta_destino = '../src/image/productos/';
      $archivo_subido = $carpeta_destino . $_FILES['img'.$_POST['idProducto']]['name'];
      move_uploaded_file($_FILES['img'.$_POST['idProducto']]['tmp_name'], $archivo_subido);
    }
    echo '
    <script>
      alertActualizarProducto()
    </script>
    ';
  } else {
    echo '
    <script>
      $msg = "Error al actualizar el producto";
      alertError($msg)
    </script>
    ';
  }
}

?>
<div class="row" style="margin-top: 5px;font-size: 19px;">
  <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="?recibos=true">Recibos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Inventario</li>
  </ol>
  </nav>
</div>               
                
<div class="btn-group">
<form action="<?php echo( htmlspecialchars($_SERVER["PHP_SELF"]) ).'?inventario=true' ?>" method="POST">
    <button type="button" class="btn btn-secondary" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-filter"></i>Filtrar</button>
    <ul class="dropdown-menu">
      <?php
        foreach ($categorias as $categoria) {
          echo '<li><button type="submit" class="dropdown-item" name="filtro-categoria" value="'.$categoria[0].'">'.$categoria[1].'</button></li>';
        }
      ?>
    </ul>
  </form>
</div>
<button type="button" class="btn btn-outline-dark dropdown-toggle" style="float: right; margin-top: 10px;" data-bs-toggle="dropdown" aria-expanded="false"></i>Modificar</button>
<ul class="dropdown-menu">
  <li><button class="dropdown-item"  data-bs-toggle="modal" data-bs-target="#agregarUnProducto">Agregar un producto</button></li>
  <li><button class="dropdown-item"  data-bs-toggle="modal" data-bs-target="#agregarVariosProductos">Agregar productos</button></li>
</ul>
<button type="button" class="btn btn-outline-dark" style="float: right; margin-top:10px; margin-right:5px;" data-bs-toggle="modal"
        data-bs-target="#agregarCategoria"></i>Agregar categoria</button>
<div class="btn-group">
  <form action="<?php echo( htmlspecialchars($_SERVER["PHP_SELF"]) ).'?inventario=true' ?>" method="POST" style="display:inline; float:right;">
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
<div class="wrapper" style="height:65vh;">
  <div class="row" style="margin: 0 0 5px 0;">
    <?php
      if(isset($_POST['filtro-categoria'])){
        $data = [
          'categoria' => $_POST['filtro-categoria'],
          'sucursal' => $_SESSION['id_sucursal']
        ];
        $input_from_db = json_decode(POST('SuperAdministrador/services/getProductsByCategory.php', $data), true);
      } else {
        $data = [
          'sucursal' => $_SESSION['id_sucursal']
        ];
        $input_from_db = json_decode(POST('SuperAdministrador/services/getAllProducts.php', $data), true);
      }
      $index = 0;
      foreach($input_from_db as $producto){
        if($index != 0 && $index% 5==0)
          echo '<div class="row" style="margin: 0 0 5px 0;">';
        if($producto[4] == null)
          $producto[4] = "default.jpg";
        echo('
          <div class="card" style="width: 12rem;">
            <div class="card-body">
              <button type="button" class="btn btn-outline-info" style="margin-right: 50px;" data-bs-toggle="modal" data-bs-target="#actualizar'.$producto[0].'"><i class="fa fa-pencil" aria-hidden="true"></i></button>
              <button type="button" class="btn btn-outline-info" style="margin: -65px 0 0 120px;" data-bs-toggle="modal" id="'.$producto[0].'" onclick="alertEliminarProducto(this.id)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
            </div>
            <img src="../src/image/productos/'.$producto[4].'" class="card-img-top" alt="imagen_'.$producto[1].'">
            <h5 class="text-center"> '. $producto[1] .'</h5>
            <h7>SKU: '.$producto[3].' </h7>
            <div class="card-body">
              <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#mostrarDetalleProducto'.$producto[0].'"><i class="fa fa-search-plus"></i></button>
            </div>
          </div>
        ');
        $index++;
        if($index != 0 && $index % 5 == 0)
          echo '</div>';

      }
      if ($input_from_db == null)
      echo('<p>No se encontraron productos</p>'); 
  
    ?>
  </div>
</div>
  <?php
  
  foreach($input_from_db as $producto){
    echo('
    <!-- Modal Detalle Producto-->
    <div class="modal fade bd-example-modal-sm" id="mostrarDetalleProducto'. $producto[0] .'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel'. $producto[0] .'">Detalle de Producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form>
              <div class="mb-3">
                <label for="vendedor" class="col-form-label">Nombre de producto</label>
                <input type="text" readonly class="form-control" id="nombreproducto'. $producto[0] .'" value="'. $producto[1] .'">
              </div>
              <div class="mb-3">
                <label for="hora" class="col-form-label">Precio <b>(SIN IVA*)</b></label>
                <input type="text" readonly class="form-control" id="precio'. $producto[0] .'" value="'. $producto[5] .'">
              </div>
              <div class="mb-3">
                <label for="productos" class="col-form-label">Categoria</label>
                <input type="text" readonly class="form-control" id="categoria'. $producto[0] .'" value="'. $producto[6] .'">
              </div>
              <div class="mb-3">
                <label for="total" class="col-form-label">En existencia</label>
                <input type="text" readonly class="form-control" id="existencia'. $producto[0] .'" value="'. $producto[2] .'">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal Actualizar producto-->
    <div class="modal fade bd-example-modal-sm" id="actualizar'.$producto[0].'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel'.$producto[0].'">Editar producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="'. htmlspecialchars($_SERVER['PHP_SELF']).'?inventario=true" method="POST" enctype = "multipart/form-data" style="display: inline;">
            <input type="hidden" name="idProducto" value="'. $producto[0] .'">
              <div class="mb-3">
                <label for="producto" class="col-form-label">Nombre de producto</label>
                <input type="text" class="form-control" id="nombreProducto'.$producto[0].'" name="nombre'.$producto[0].'" value="'. $producto[1] .'" required>
              </div>
              <div class="mb-3">
                <label for="costo" class="col-form-label">Costo</label>
                <input type="text" class="form-control" id="costo'. $producto[0] .'" name="costo'.$producto[0].'" value="'. $producto[8] .'" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 46) event.returnValue = false;" required>
              </div>
              <div class="mb-3">
                <label for="precio" class="col-form-label">Precio <b>(SIN IVA*)</b></label>
                <input type="text" class="form-control" id="precio'. $producto[0] .'" name="precio'.$producto[0].'" value="'. $producto[5] .'" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 46) event.returnValue = false;" required>
              </div>
              <div class="mb-3">
                <label for="formFile" class="form-label">Imagen producto</label>
                <input class="form-control" name="img'.$producto[0].'" type="file" id="formFile'.$producto[0].'" accept="image/png, image/jpeg">
              </div>
              <div class="mb-3">
                <label for="categoria" class="col-form-label">Categoria</label>
                <select class="form-control" id="categoria'. $producto[0] .'" name="categoria'.$producto[0].'" required>
                  <option value="">Seleccione una categoria</option>
                ');
                foreach($categorias as $categoria){
                  if($categoria[1] == $producto[6])
                    echo('<option value="'. $categoria[0] .'" selected>'. $categoria[1] .'</option>');
                  else
                    echo('<option value="'. $categoria[0] .'">'. $categoria[1] .'</option>');
                }
              echo ('
                </select>
              </div>
              <div class="mb-3">
                <label for="proveedor" class="col-form-label">Proveedor</label>
                <select class="form-control" id="proveedor'. $producto[0] .'" name="proveedor'.$producto[0].'" required>
                  <option value="">Seleccione un proveedor</option>
                ');
                foreach($proveedores as $proveedor){
                  if($proveedor[0] == $producto[7])
                    echo('<option value="'. $proveedor[0] .'" selected>'. $proveedor[1] .'</option>');
                  else
                    echo('<option value="'. $proveedor[0] .'">'. $proveedor[1] .'</option>');
                }
              echo ('
                </select>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" name="edit-product" class="btn btn-success">Aceptar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  ');
  }
  
  ?>
        
<!-- Modal Agregar un producto-->
<div class="modal fade bd-example-modal-sm" id="agregarUnProducto" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Agregar un  producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']).'?inventario=true' ?>" method="POST" enctype = "multipart/form-data" style="display: inline;">
          <div class="mb-3">
            <label for="producto" class="col-form-label">Nombre de producto</label>
            <input type="text" class="form-control" id="nombreproducto" name="nombre" required>
          </div>
          <div class="mb-3">
            <label for="formFile" class="form-label">Imagen producto</label>
            <input class="form-control" type="file" id="formFile" accept="image/png, image/jpeg" name="img">
          </div>
          <div class="mb-3">
            <label for="costo" class="col-form-label">Costo</label>
            <input type="text" class="form-control" id="costo" name="costo" required>
          </div>
          <div class="mb-3">
            <label for="precio" class="col-form-label">Precio <b>(SIN IVA*)</b></label>
            <input type="text" class="form-control" id="precio" name="precio" required>
          </div>
          <div class="mb-3">
            <label for="categoria" class="col-form-label">Categoria</label>
            <select class="form-control" id="categoria" name="categoria" required>
              <option value="" selected>Seleccione una categoria</option>
            <?php
              foreach($categorias as $categoria){
                echo('<option value="'. $categoria[0] .'">'. $categoria[1] .'</option>');
              }
            ?>
            </select>
            <!-- <input type="text" class="form-control" id="categoria" name="categoria"> -->
          </div>
          <div class="mb-3">
            <label for="servicio" class="col-form-label">Es un servicio:</label>
            <select class="form-control" id="select_servicio" name="servicio" required>
              <option value="">Selecciona una opcion</option>
              <option value="0">No</option>
              <option value="1">Sí</option>
            </select>
          </div>
          <!-- <div class="mb-3" id="existeniaDiv">
            <label for="existencia" class="col-form-label">En existencia</label>
            <input type="text" class="form-control" id="existencia" name="existencia" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" required>
          </div> -->
          <div class="mb-3">
            <label for="proveedor" class="col-form-label">Proveedor</label>
            <select class="form-control" id="proveedor" name="proveedor" required>
              <option value="" selected>Seleccione un proveedor</option>
            <?php
              foreach($proveedores as $proveedor){
                echo('<option value="'. $proveedor[0] .'">'. $proveedor[1] .'</option>');
              }
            ?>
            </select>
            <!-- <input type="text" class="form-control" id="proveedor"> -->
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" name="addProduct" class="btn btn-success">Aceptar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Agregar Proveedor-->
<div class="modal fade bd-example-modal-xl" id="agregarCategoria" data-bs-backdrop="static"
  data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Agregar categoria</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']).'?inventario=true'?>" method="POST">
          <div class="row">
            <div class="mb-3 col">
              <label for="nombre" class="col-form-label">Nombre:</label>
              <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col">
              <label for="iva" class="col-form-label">Tiene iva:</label>
              <select name="iva" class="form-control" id="iva" required>
                <option value="">Seleccione una opcion</option>
                <option value="0">No</option>
                <option value="1">Si</option>
              </select>
            </div>
            <div class="mb-3 col">
              <label for="ieps" class="col-form-label">IEPS:</label>
              <input type="text" class="form-control" id="ieps" name="ieps">
            </div>
            <div class="mb-3 col">
              <label for="isr" class="col-form-label">ISR:</label>
              <input type="text" class="form-control" id="isr" name="isr">
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col">
              <label for="descripcion" class="form-label">Descripcion:</label>
              <textarea class="form-control" id="descripcion" rows="3" name="descripcion"></textarea>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success" name="addCategoria">Agregar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- //*================================ -->
<!-- // * Modal Agregar varios productos -->
<!-- //*================================ -->
<div class="modal fade bd-example-modal-lg" id="agregarVariosProductos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <form action="<?php echo( htmlspecialchars($_SERVER["PHP_SELF"]) ).'?inventario=true' ?>" enctype = "multipart/form-data" method="POST">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Seleccionar archivo .CSV</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="file" name="adjunto" accept=".csv"/>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
          <button type="summit" class="btn btn-success" name="cargar_csv">Cargar</button>
        </div>
      </div>
    </div>
  </form>
</div>
