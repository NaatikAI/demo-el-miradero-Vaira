<?php
  $errores = false;

  if(isset($_POST['setRegion'])){
    // var_dump($_POST);
    $data = [
      'sucursal' => $sucursal,
      'region' => $_POST['region']
    ];
    $response = json_decode(Post("Administrador/services/setRegionSucursal.php",$data),true);
    // var_dump($response);
    if($response[0] == 'Success')
      echo '
      <script>
        alertConfigImpuesto()
      </script>';  
    else
      echo '
      <script>
        alertConfigImpuestoError()
      </script>';
  }

  if(isset($_POST['add-punto-venta'])){
    $data = [
      'sucursal' => $sucursal,
      'nombre' => $_POST['nombre']
    ];

    $agregar = json_decode(POST("Administrador/services/addPuntoVenta.php",$data), true);

    if($agregar[0] > 0)   
      echo('
        <script>
          alertAgregarPunto()
        </script>
      ');
  }

  if (isset($_POST['agregarUsuario'])){
    $nombre = $_POST['nombre'];
    $apellidoP = $_POST['apellidoP'];
    $apellidoM = $_POST['apellidoM'];
    $usuario = $_POST['usuario'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $pass = $_POST['password'];
    $pass2 = $_POST['password2'];
    $rol = $_POST['rol'];
    $_sucursal = $sucursal;

    if(empty($nombre) || empty($apellidoP) || empty($apellidoM) || empty($usuario) || empty($correo) 
        || empty($telefono) || empty($pass) || empty($pass2) || empty($rol))
      echo('
        <script>
          alertCamposVacios()
        </script>
      ');
    else {
      $stmt = $pdo->prepare('SELECT * FROM usuario WHERE usuario = :usuario LIMIT 1;');
      $stmt->execute(array(':usuario' => $_POST['usuario']));
      $resultado = $stmt->fetch(); 
    
      if($resultado != false){
        echo('
          <script>
            alertUsuarioExistente()
          </script>
        ');
        $errores = true;
      }
        
        if($pass != $pass2){
          echo('
            <script>
              alertPassDiferente()
            </script>
          ');
          $errores = true;
        }

        if(!$errores){
          $data = [
            'nombre' => $nombre,
            'apellidoP' => $apellidoP,
            'apellidoM' => $apellidoM,
            'usuario' => $usuario,
            'correo' => $correo,
            'telefono' => $telefono,
            'password' => $pass,
            'rol' => $rol,
            'sucursal' => $_sucursal
          ];

          $insertarUsuario = json_decode(POST("Administrador/services/addUser.php",$data), true);
          if($insertarUsuario[0] == "Success")
            echo('
              <script>
                alertAgregarUsuario()
              </script>
            ');
        }
    }
  }

  if(isset($_GET["id"])){
    $data = [
      'idUsuario' => $_GET["id"]
    ];

    // var_dump($data);
    $eliminar = json_decode(POST("Administrador/services/deleteUser.php",$data), true);
    if($eliminar[0] > 0)
    echo "<script>Swal.fire({
      title: 'Usuario eliminado!',
      icon: 'success',
        confirmButtonText: 'Ok'
      }).then((result)=>{
        if(result.isConfirmed){
          window.location.href='index.php?configuracion=true';
        }
      }) </script>";
  }

  if(isset($_POST['updatePuntos'])){
    $idUsuario = $_POST['id_usuario'];
    $data = [
      'idUsuario' => $idUsuario,
      'puntos' => []
    ];
    foreach($_POST['idPunto'] as $punto){
      $data['puntos'][] = ['idPunto' => $punto];
    }
    $updatePuntos = json_decode(POST("Administrador/services/updatePuntos.php",$data), true);

    if($updatePuntos[0] == 'Success')
      echo "<script>Swal.fire({
        title: 'Puntos actualizados!',
        icon: 'success',
          confirmButtonText: 'Ok'
        }).then((result)=>{
          if(result.isConfirmed){
            window.location.href='index.php?configuracion=true';
          }
        }) </script>";
  }


  if(isset($_POST['updateUser'])){
    $idUsuario = $_POST['id_usuario'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $pass = $_POST['pass'];
    $pass2 = $_POST['pass2'];

    if($pass != $pass2){
      echo('
        <script>
          alertPassDiferente()
        </script>
      ');
    } else {
      $data = [
        'idUsuario' => $idUsuario,
        'correo' => $correo,
        'telefono' => $telefono,
        'password' => $pass
      ];
      $actualizar = json_decode(POST("Administrador/services/updateUser.php",$data), true);
      if($actualizar[0] = 'Success')
        echo('
          <script>
            alertEdicionSatisfactoriaUsuario()
          </script>
        ');
    }
  }

  $data = [];

  $regiones = json_decode(POST("Administrador/services/getRegions.php",$data), true);

  // var_dump($region);
?>

<div class="row" style="margin-top: 5px;font-size: 19px;">
  <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="?recibos=true">Recibos</a></li>
      <li class="breadcrumb-item active" aria-current="page">Configuraci&oacuten</li>
    </ol>
  </nav>
</div>
<div class="row" style="font-size: 25px; margin-top: 15px;">
  <div>
    Usuarios
    <button type="button" class="btn btn-outline-dark" style="float: right; margin-left:5px" data-bs-toggle="modal" data-bs-target="#configImpuestos"></i>Configurar impuestos</button>
    <button type="button" class="btn btn-outline-dark" style="float: right; margin-left:5px" data-bs-toggle="modal" data-bs-target="#agregarPuntoDeVenta"></i>Agregar Punto de venta</button>
    <button type="button" class="btn btn-outline-dark" style="float: right;" data-bs-toggle="modal" data-bs-target="#agregarUsuario"></i>Agregar usuario</button> 
  </div>
</div>
<div class="row" style="margin-top: 15px; font-size: 17px;">
  <table class="table">
    <thead>
      <tr>
        <th scope="col">Nombre</th>
        <th scope="col">Correo</th>
        <th scope="col">Telefono</th>
        <th scope="col">Usuario</th>
        <th scope="col">Sucursal</th>
        <th scope="col">Rol</th>
        <th scope="col">Eliminar</th>
        <th scope="col">Editar</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $data = [
        'idAdmin' => $id_usuario
      ];
      $input_from_db = json_decode(POST("Administrador/services/getUsers.php",$data), true);

      foreach($input_from_db as $value){
        echo('
          <tr>
            <th scope="row">'. $value[1] .'</th>
            <td>'. $value[2] .'</td>
            <td>'. $value[3] .'</td>
            <td>'. $value[4] .'</td>
            <td>'. $value[5] .'</td>
            <td>'. strtolower ($value[6]) .'</td>
            <td><button type="button" class="btn btn-danger" id="'. $value[0] .'" onclick="alertElimarUsuario(this.id)" style="float: center;"><i class="fa fa-minus-circle"></i></button></td>
            <td><button type="button" class="btn btn-success" style="float: center;" data-bs-toggle="modal" data-bs-target="#editarUsuario'.$value[0].'"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>
          </tr>
      ');
      }

      ?>
    </tbody>
  </table>
</div>

  <!-- Modal Agregar Usuario-->
  <div class="modal fade bd-example-modal-xl" id="agregarUsuario" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Agregar usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']).'?configuracion=true'?>" method="POST">
          <?php 
            if($errores){
              echo('        
                <div class="mb-3">
                  <label for="nombre" class="col-form-label">Nombre:</label>
                  <input type="text" class="form-control" id="nombre" name="nombre" value="'. $nombre .'" required>
                </div>
                <div class="mb-3">
                  <label for="apellidoP" class="col-form-label">Apellido paterno:</label>
                  <input type="text" class="form-control" id="apellidoP" name="apellidoP" value="'. $apellidoP .'" required>
                </div>
                <div class="mb-3">
                  <label for="apellidoM" class="col-form-label">Apellido materno:</label>
                  <input type="text" class="form-control" id="apellidoM" name="apellidoM" value="'. $apellidoM .'" required>
                </div>
                <div class="mb-3">
                  <label for="usuario" class="col-form-label">Usuario:</label>
                  <input type="text" class="form-control" id="usuario" name="usuario" value="'. $usuario .'" required>
                </div>
                <div class="mb-3">
                  <label for="correo" class="col-form-label">Correo:</label>
                  <input type="email" class="form-control" id="correo" name="correo" value="'. $correo .'" required>
                </div>
                <div class="mb-3">
                  <label for="telefono" class="col-form-label">Tel&eacutefono:</label>
                  <input type="text" class="form-control" id="teleforno" name="telefono" value="'. $telefono .'" minlength="10" maxlength="10" required>
                </div>
                <div class="mb-3">
                  <label for="contrasena" class="col-form-label">Contraseña:</label>
                  <input type="password" class="form-control" id="contrasena" name="password" minlength="8" required>
                </div>
                <div class="mb-3">
                  <label for="contrasena2" class="col-form-label">Confirmar contraseña:</label>
                  <input type="password" class="form-control" id="contrasena2" name="password2" minlength="8" required>
                </div>
                <div class="mb-3">
                  <label for="rol" class="col-form-label">Rol:</label>
                  <select name="rol" id="rol">');
                    $data = [
                      'idTipo' => $user_type
                    ];

                    // var_dump($data);
                    $input_from_db = json_decode(POST("Administrador/services/getRoles.php",$data), true);

                    foreach($input_from_db as $value){
                      echo('
                          <option value="'.$value[0].'">'.strtolower($value[1]).'</option>
                      ');
                    }
                echo('
                  </select>
                </div>
              </div>
            ');
          } else{
              echo('        
                <div class="mb-3">
                  <label for="nombre" class="col-form-label">Nombre:</label>
                  <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                  <label for="apellidoP" class="col-form-label">Apellido paterno:</label>
                  <input type="text" class="form-control" id="apellidoP" name="apellidoP" required>
                </div>
                <div class="mb-3">
                  <label for="apellidoM" class="col-form-label">Apellido materno:</label>
                  <input type="text" class="form-control" id="apellidoM" name="apellidoM" required>
                </div>
                <div class="mb-3">
                  <label for="usuario" class="col-form-label">Usuario:</label>
                  <input type="text" class="form-control" id="usuario" name="usuario" required>
                </div>
                <div class="mb-3">
                  <label for="correo" class="col-form-label">Correo:</label>
                  <input type="email" class="form-control" id="correo" name="correo" required>
                </div>
                <div class="mb-3">
                  <label for="telefono" class="col-form-label">Tel&eacutefono:</label>
                  <input type="text" class="form-control" id="teleforno" name="telefono" minlength="10" maxlength="10" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" required>
                </div>
                <div class="mb-3">
                  <label for="contrasena" class="col-form-label">Contraseña:</label>
                  <input type="password" class="form-control" id="contrasena" name="password" minlength="8" required>
                </div>
                <div class="mb-3">
                  <label for="contrasena2" class="col-form-label">Confirmar contraseña:</label>
                  <input type="password" class="form-control" id="contrasena2" name="password2" minlength="8" required>
                </div>
                <div class="mb-3">
                  <label for="rol" class="col-form-label">Rol:</label>
                  <select name="rol" id="rol">');
                    $data = [
                      'idTipo' => $user_type
                    ];

                    // var_dump($data);
                    $input_from_db = json_decode(POST("Administrador/services/getRoles.php",$data), true);

                    foreach($input_from_db as $value){
                      echo('
                          <option value="'.$value[0].'">'.strtolower($value[1]).'</option>
                      ');
                    }
                echo('
                  </select>
                </div>
              </div>
            ');
          }
          ?>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success" name="agregarUsuario">Agregar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Agregar Punto de Vententa-->
  <div class="modal fade bd-example-modal-xl" id="agregarPuntoDeVenta" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Agregar punto de venta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']).'?configuracion=true'?>" method="POST">
            <div class="mb-3">
              <label for="nombre" class="col-form-label">Nombre del punto de venta:</label>
              <input type="text" class="form-control" name="nombre" id="nombre" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success" name="add-punto-venta">Agregar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Configuración de impuestos-->
  <div class="modal fade bd-example-modal-xl" id="configImpuestos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Configuración de impuestos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']).'?configuracion=true'?>" method="POST" style="display: inline;">
            <div class="mb-3">
              <label for="hora" class="col-form-label">Selecciona la región</label>
              <br>
              <div class="btn-group">
                <div class="btn-group">
                  <select name="region" id="regionIVA" class="form-select" required>
                    <option value="">Region</option>
                    <?php
                    // var_dump($regiones);
                    foreach($regiones as $region){
                      if($region[0] == $region_iva)
                        echo('
                          <option value="'.$region[0].'" name="'.$region[1].'" selected>'.$region[2].'</option>
                        ');
                      else
                        echo('
                          <option value="'.$region[0].'" name="'.$region[1].'">'.$region[2].'</option>
                          
                        ');
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="hora" class="col-form-label">IVA</label>
              <br>
              <input type="text" class="form-control" name="iva" id="iva" value="" disabled>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success" name="setRegion">Aceptar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Editar Usuario-->
  <?php
    $data = [
      "sucursal" => $sucursal
    ];

    $input_from_db = json_decode(POST("Administrador/services/getInfoUsers.php",$data), true);
    $puntos_venta = json_decode(POST("Administrador/services/getPuntosVenta.php",$data), true);
    foreach($input_from_db as  $value){
      echo('
        <div class="modal fade bd-example-modal-xl" id="editarUsuario'.$value[0].'" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Editar usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form action="'. htmlspecialchars($_SERVER['PHP_SELF']). '?configuracion=true" method="POST">
                  <input type="hidden" name="id_usuario" value="'.$value[0].'">
                  <div class="mb-3">
                    <label for="nombre" class="col-form-label">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" value="'. $value[1] .'" disabled>
                  </div>
                  <div class="mb-3">
                    <label for="apellidoP" class="col-form-label">Apellido paterno:</label>
                    <input type="text" class="form-control" id="apellidoP" value="'. $value[2] .'" disabled>
                  </div>
                  <div class="mb-3">
                    <label for="apellidoM" class="col-form-label">Apellido materno:</label>
                    <input type="text" class="form-control" id="apellidoM" value="'. $value[3] .'" disabled>
                  </div>
                  <div class="mb-3">
                    <label for="usuario" class="col-form-label">Usuario:</label>
                    <input type="text" class="form-control" id="usuario" value="'. $value[4] .'" disabled>
                  </div>
                  <div class="mb-3">
                    <label for="rol" class="col-form-label">Rol:</label>
                    <input type="text" class="form-control" id="rol" value="'. strtolower($value[5]) .'" disabled>
                  </div>
                  <div class="mb-3">
                    <label for="correo" class="col-form-label">Correo:</label>
                    <input type="email" class="form-control" id="correo" name="correo" value="'. $value[6] .'" required>
                  </div>
                  <div class="mb-3">
                    <label for="telefono" class="col-form-label">Tel&eacutefono:</label>
                    <input type="text" class="form-control" id="teleforno" name="telefono" value="'. $value[7] .'" minlength="10" maxlength="10" onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" required">
                  </div>
                  <div class="mb-3">
                    <label for="password" class="col-form-label">Contraseña:</label>
                    <input type="password" class="form-control" name="pass" id="contrasena" minlength="8">
                  </div>
                  <div class="mb-3">
                    <label for="pass2" class="col-form-label">Confirmar contraseña:</label>
                    <input type="password" class="form-control" name="pass2" id="contrasena" minlength="8">
                  </div>
                  <div class="btn-group">
                    <label for="puntodeventa" class="col-form-label">Punto de venta:</label>
                    <button type="button" class="btn btn-outline-secondary" style="margin-left: 5px;" data-bs-toggle="modal" data-bs-target="#puntosVenta'. $value[0] .'" aria-expanded="false"><i class="fa fa-arrow-right"></i></button>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                  <button type="submit" class="btn btn-success" name="updateUser">Aceptar</button>
                </div>
              </form>
            </div>
          </div>
        </div>

  <!-- Modal Puntos de venta -->
  <div class="modal fade" id="puntosVenta'. $value[0] .'" tabindex="-1" aria-labelledby="puntosVentaLabel" aria-hidden="true">
    <div class="modal-dialog">
    <form action="'. htmlspecialchars($_SERVER['PHP_SELF']). '?configuracion=true" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="puntosVentaLabel">Puntos de venta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id_usuario" value="'.$value[0].'">');
              foreach($puntos_venta as $puntos){
                if($puntos[2] == $value[0])
                  echo('
                  <li style="list-style:none;">
                    <label> 
                      <input  value="'. $puntos[0] .'" class="form-check-input" type="checkbox" id="punto'. $puntos[0] .'" name="idPunto[]" checked> '.$puntos[1].'
                    </label>
                  </li>
                  ');
                else if ($puntos[2] == null)
                  echo('
                  <li style="list-style:none;">
                    <label>
                      <input  value="'. $puntos[0] .'" class="form-check-input" type="checkbox" id="punto'. $puntos[0] .'" name="idPunto[]'. $puntos[0] .'"> '.$puntos[1].'
                    </label>
                  </li>
                  ');
              }
              echo('
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" name="updatePuntos" class="btn btn-success">Guardar</button>
          </div>
          </div>
        </div>
      </form>
    </div>
    ');
  }
  ?>
  <!-- Modal Confirmación editar -->
  <div class="modal fade bd-example-modal-sm" id="confirmarEditar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">¿Est&aacute;s seguro que deseas editar el usuario?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-dismiss="modal"  onclick="alertEdicionSatisfactoriaUsuario()">S&iacute;</button>
        </div>
      </div>
    </div>
  </div>

</div>


<script>
  let regionIVA = document.getElementById('regionIVA');
  let iva = document.getElementById('iva');

  let valIVA = regionIVA.options[regionIVA.selectedIndex].getAttribute('name');
  if(regionIVA.value != "Region" && valIVA != null)
    iva.value = valIVA * 100 + "%";
  else
    iva.value = "";
    
  regionIVA.addEventListener("change", function(){
    let valIVA = regionIVA.options[regionIVA.selectedIndex].getAttribute('name');    
    if(regionIVA.value != "Region" && valIVA != null)
      iva.value = valIVA * 100 + "%";
    else
      iva.value = "";
  })

</script>