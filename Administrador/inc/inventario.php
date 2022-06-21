<?php

if(isset($_POST['edit-product'])){
  $data = [
    'fkUsuario' => $id_usuario,
    'fkSucursal' => $sucursal,
    'fkProducto' => $_POST['idProducto'],
    'cantidad' => $_POST['existencia']
  ];
  // var_dump($data);
  $reponse = json_decode(Post("Administrador/services/updateProductsInventory.php",$data),true);
  // var_dump($reponse);
  if($reponse[0] == 'SUCCESS')
    echo '
    <script>
      actualizarProductoInventarioExito()
    </script>';  
  else
    echo '
    <script>
      $msg = "'. $reponse[0] .'"
      actualizarProductoInventarioError($msg)
    </script>';
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

<div class="row-1">
  <form action="<?php echo( htmlspecialchars($_SERVER["PHP_SELF"]) ).'?inventario=true' ?>" method="POST">
    <button type="button" class="btn btn-secondary" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-filter"></i>Filtrar</button>
    <ul class="dropdown-menu">
      <?php
        foreach ($categorias as $categoria) {
          echo '<li><button type="submit" class="dropdown-item" name="categoria" value="'.$categoria[0].'">'.$categoria[1].'</button></li>';
        }
      ?>
    </ul>
  </form>
</div>
<div class="wrapper" style="height:65vh;">
<?php
  if (isset($_POST['categoria'])) {
    $data = [
      "sucursal" => $sucursal,
      "categoria" => $_POST['categoria']
    ];
    $input_from_db = json_decode(Post("Administrador/services/getFilters.php",$data),true);
  } else {
    $data = [
      "sucursal" => $sucursal
    ];
    $input_from_db = json_decode(POST("Administrador/services/getAllProducts.php", $data), true);
  }

  // var_dump($input_from_db);

  $index = 0;
  foreach($input_from_db as $producto){
    if($index % 5 == 0)
      echo '<div class="row" style="margin: 0 0 5px 0;">';
    if($producto[4] == null)
      $producto[4] = "default.jpg";
    echo('
      <div class="card" style="width: 12rem;">
        <img src="../src/image/productos/'. $producto[4] .'" class="card-img-top" alt="imagen_'. $producto[1] .'">
        <h5 class="text-center"> '. $producto[1] .'</h5>
        <h7>SKU: '.$producto[3].' </h7>
        <div class="card-body">
          <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#mostrarDetalleProducto'. $producto[0] .'"><i class="fa fa-search-plus"></i></button>
          <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#actualizar'. $producto[0] .'"><i class="fa fa-pencil" aria-hidden="true"></i></button>
        </div>
      </div>      
    ');
    $index++;
    if($index != 0 && $index % 5 == 0)
      echo '</div>';
  }
      
  if ($input_from_db == null)
    echo('<p>No se encontraron productos</p>'); 

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
                <label for="hora" class="col-form-label">Precio</label>
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
    <div class="modal fade bd-example-modal-sm" id="actualizar'. $producto[0] .'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel'. $producto[0] .'">Editar producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="'. htmlspecialchars($_SERVER['PHP_SELF']).'?inventario=true" method="POST" style="display: inline;">
              <input type="hidden" name="idProducto" value="'. $producto[0] .'">
              <div class="mb-3">
                <label for="producto" class="col-form-label">Nombre de producto</label>
                <input type="text" class="form-control" name="nombre" id="nombreproducto'. $producto[0] .'" value="'. $producto[1] .'" disabled>
              </div>
              <div class="mb-3">
                <label for="precio" class="col-form-label">Precio</label>
                <input type="text" class="form-control" name="precio" id="precio'. $producto[0] .'" value="'. $producto[5] .'" disabled>
              </div>
              <div class="mb-3">
                <label for="categoria" class="col-form-label">Categoria</label>
                <input type="text" class="form-control" name="categoria" id="categoria'. $producto[0] .'" value="'. $producto[6] .'" disabled>
              </div>
              <div class="mb-3">
                <label for="existencia" class="col-form-label">En existencia</label>
                <input type="text" class="form-control" name="existencia" id="existencia'. $producto[0] .'" value="'. $producto[2] .'" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" required>
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
</div>