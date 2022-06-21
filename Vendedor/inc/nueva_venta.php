<div class="row" style="margin-top: 5px;font-size: 19px;">
  <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page">Nueva venta</li>
    </ol>
  </nav>
</div>
<!-- //* Validamos que el usuario tenga un punto de venta asignado -->
<?php if(isset($_SESSION['id_punto_de_venta'])): ?>  
  <div class="row">
    <form action="<?php htmlspecialchars($_SERVER['PHP_SELF']).'?nueva_venta=true'?>" class="col-9 buscar" method="POST">
      <input id="buscar_producto_entrada" name="busqueda" type="text" placeholder="Buscar producto">
      <button type="submit" class="btn btn-primary fa fa-search" style="padding: 5px 12px; margin-top:-.8%;"></button>
    </form>
    <form action="<?php echo( htmlspecialchars($_SERVER["PHP_SELF"]) ).'?nueva_venta=true' ?>" method="POST" class="col-3">
    <button type="button" class="btn btn-secondary"  data-bs-toggle="dropdown" style="margin-left: 29%; margin-top:-.8%;"><i class="fa fa-filter"></i>Filtrar</button>
      <ul class="dropdown-menu">
        <?php
        foreach ($categorias as $categoria) {
          echo '<li><button type="submit" class="dropdown-item" name="categoria" value="'.$categoria[0].'">'.$categoria[1].'</button></li>';
        }
        ?>
      </ul>
    </form>
  </div>
  <div class="wrapper"  style="height:65vh;">
    <?php
    // * Validamos que la sucursal exista
    if ($sucursal != null){
      $data = [
        "sucursal" => $sucursal[0]
      ];
      $info_sucursal = json_decode(Post("Vendedor/services/getInfoSucursal.php",$data),true);
    } else
      $data = [
        "sucursal" => null
      ];
    
    if(isset($_POST['busqueda'])){
      $data = [
        "sucursal" => $sucursal[0],
        "busqueda" => trim($_POST['busqueda'])
      ];
      $input_from_db = json_decode(Post("Vendedor/services/getSearch.php",$data),true);
    } else if (isset($_POST['categoria'])) {
      $data = [
        "sucursal" => $sucursal[0],
        "categoria" => $_POST['categoria']
      ];
      $input_from_db = json_decode(Post("Vendedor/services/getFilters.php",$data),true);
    } else {
      $input_from_db = json_decode(Post("Vendedor/services/getAllProducts.php",$data),true);
    }
    $index = 0;
      foreach($input_from_db as $value){
        if($index % 3 == 0){
          echo('
            <div class="row" style="margin: 0 0 5px 0;">
          ');
        }
        if($value[4]==null)
          $value[4] = "default.jpg";
        echo('
            <div class="card" style="width: 12rem;">
              <form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'?nueva_venta=true" method="POST">

                  <input hidden name="id_producto" value='.$value[0].'> </input>
                  <input hidden name="cantidad" value=1> </input>
                  <input hidden name="nombre_producto" value="'.$value[1].'"> </input>
                  <input hidden name="precio_unitario" value="'.$value[5].'"> </input>
                  <input hidden name="sku_producto" value="'.$value[3].'"> </input>
                  <input hidden name="id_punto_de_venta" value="'.$_SESSION['id_punto_de_venta'].'"> </input>
                  
                  <img src="'.'../src/image/productos/'.$value[4].'" class="card-img-top" alt="...">
                  <div class="card-body">
                    <h5 class="card-title">'.$value[1].'</h5>
                    <h7 class="card-title">cantidad: '.$value[2].'</h7> <br>
                    <h7 class="card-title">sku:'.$value[3].'</h7> <br>
                    <h6 class="card-title">precio: $'.round($value[5],2).'</h6>
                    <button type="submit" name="add_to_cart" value="true" class="btn btn-primary">Agregar a carrito</button>
                  </div>
              </form>

            </div>
        ');
        $index++;
        if($index != 0 && $index % 3 == 0){
          echo('
            </div>
          ');
        }
      }
      if($index % 3 != 0){
        echo('
          </div>
        ');
      }

      if ($input_from_db == null)
        echo('<p>No se encontraron productos</p>');
    ?>
    
  </div>

</div> <!-- This div closes 'col' for 'indexvendedor.php' to start a new one below -->


<div class="col-4" style="height:80vh; overflow-y: scroll;" >
  <h1><?php echo(trim($_SESSION['nombre_punto_de_venta']))  ?></h1>

      

      <?php
        $data = [
          "usuario" => $id_usuario,
          "punto" => trim($_SESSION['id_punto_de_venta'])
        ];  

        $index = 0;
        echo('
        <form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'?nueva_venta=true" method="POST" style="min-height: 0px; max-height:0px;">
          <div class="btn-group">
            <div class="btn-group">
              <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"> Punto de venta </button>
              <ul class="dropdown-menu">');
              foreach($id_punto_de_venta as $punto){
                echo(
                  "<li><button type='submit' class='dropdown-item' name='punto_de_venta' value='$punto[0]'>$punto[1]</button></li>"
                );
              }
              echo('</ul>
              </div> 
            </div>
          </form>
        ');
        echo('<br><br><br>
        <script>
          var subtotal_acumulado = 0
         
        </script>
        ');
        if(isset($_SESSION['cart'][$_SESSION['id_punto_de_venta']])){
          if(isset($_POST["remove_product_from_id"])){
            foreach ($_SESSION["cart"][$_SESSION['id_punto_de_venta']] as $clave => $valor){
              if($valor["id_producto"] == $_POST["remove_product_from_id"]){
                unset($_SESSION["cart"][$_SESSION['id_punto_de_venta']][$clave]);
                unset($_POST["remove_product_from_id"]);
              }
            }
          }
          foreach($_SESSION['cart'][$_SESSION['id_punto_de_venta']] as $value){
            echo('
              <div class="row justify-content-md-center">
                <div class="col-sm-2" >
                  <input id="ticket_product_'.$index.'_cant" type="number" min="1" name="cantidad_actual_producto" value="'.$value["cantidad"].'" style="width:100%;">
                </div>
                <div class="col-sm-5">
                  <h5>'.$value["nombre_producto"].'</h5>
                </div>
                <div  class="col-sm-4">
                  <h4 id="ticket_product_'.$index.'_cost">$</h4>
                </div>
                <div  class="col-sm-1">
                <form method="POST">
                  <button type="submit" class="btn btn-danger btn-sm" name=remove_product_from_id value='.$value["id_producto"].' ><i class="fa fa-trash"></i></button>
                </form >
                  <input hidden id="ticket_product_'.$index.'_price" value="'.$value["precio_unitario"].'">
                </div>
              </div>
            <script>
              let ticket_product_'.$index.'_cant = document.getElementById("ticket_product_'.$index.'_cant")
              var product_cost_'.$index.'  = 0
              ticket_product_'.$index.'_cant.addEventListener("change", function() {
                product_cost_'.$index.' = ticket_product_'.$index.'_cant.value * document.getElementById("ticket_product_'.$index.'_price").value

                fetch("./services/updateCartExternal.php?nueva_cantidad="+ ticket_product_'.$index.'_cant.value+"&id_producto="+'.$value["id_producto"].', {
                  method: "GET",
                }).then(
                    response => response.json()
                ).then(
                    response => console.log()
                ).catch(
                    error => console.log(error)
                )

                document.getElementById("ticket_product_'.$index.'_cost").innerHTML = "$ "+ (product_cost_'.$index.').toFixed(2)
                updateTicket()
              })
              product_cost_'.$index.' = ticket_product_'.$index.'_cant.value * document.getElementById("ticket_product_'.$index.'_price").value
              document.getElementById("ticket_product_'.$index.'_cost").innerHTML = "$ "+ (product_cost_'.$index.').toFixed(2)
            </script>
            ');
            $index++;
          }
          if(count($_SESSION["cart"][$_SESSION['id_punto_de_venta']] ) > 0){
            echo('
              <br>
              <div class="row justify-content-md-center">
                <div class="col-sm-6" >
                  <h4 id="ticket_IVA">$ 00</h4>
                  <h4 id="ticket_total">$ 00</h4>
                </div>
              </div>
            

                  <br><br>
                  <div class="row justify-content-md-center">
                    <div class="col-sm-6" >
                    <button style="margin-bottom: 25px;" id="generate_ticket_button" type="button" class="btn btn-primary">Generar venta</button>
                    </div>
                  </div>
                  

              <script>
              let ticket_subtotal = document.getElementById("ticket_subtotal")
              let ticket_IVA = document.getElementById("ticket_IVA")
              let ticket_total = document.getElementById("ticket_total")

              var global_total = global_iva = idVenta = 0
              let updateTicket= () =>{
                var subtotal = 0

                for(var i = 0; i < '.$index.'; i++){
                  subtotal += eval("product_cost_" + i)
                }
                global_total = (subtotal).toFixed(2)
                global_iva = (subtotal*.16).toFixed(2)
                ticket_IVA.innerHTML = "IVA $" + global_iva
                ticket_total.innerHTML = "Total $" + global_total
              }
              updateTicket()

              let generate_ticket_button = document.getElementById("generate_ticket_button")
              generate_ticket_button.addEventListener("click",() => {
                let data = JSON.stringify({
                  "fkUsuario":"'.$id_usuario.'",
                  "fkPunto":"'.trim($_SESSION['id_punto_de_venta']).'",
                  "fkTipoPago":"3", // Este parametro es estatico de momento, faltaria añadir el modulo para tarjetas de credito
                  "productos":['); 
                  
                  foreach($_SESSION['cart'][$_SESSION['id_punto_de_venta']] as $value){
                    if ($value === end($_SESSION['cart'][$_SESSION['id_punto_de_venta']])){
                      echo('
                        {
                          "sku": "'.$value["sku_producto"].'",
                          "cantidad": '.$value["cantidad"].'
                        }
                      ');
                    } else{
                        echo('
                          {
                            "sku":"'. $value["sku_producto"]. '",
                            "cantidad":"'. $value["cantidad"]. '"
                          },
                        ');
                    }
                  };
  
                echo(']});

                fetch("./services/generateSale.php/", {
                  method: "POST",
                  body: data
                }).then(
                    response => response.json()
                ).then(
                    data => idVenta = data
                ).then(
                    response => console.log(response)
                ).catch(
                    error => console.log(error)
                )

                console.log(idVenta)

                Swal.fire({
                  title: "¿Deseas imprimir el ticket?",
                  showDenyButton: true,
                  confirmButtonColor: "#198754",
                  confirmButtonText: "Imprimir",
                  denyButtonText: "Cancelar",
                }).then((result) => {
                  if (result.isConfirmed) {
                    
                    window.location.assign(`./services/downloadTicket.php?productos=`+
                    `'.urlencode( json_encode($_SESSION["cart"][$_SESSION['id_punto_de_venta']]) ).'`+
                    `&nombre_tienda=`+`'.$info_sucursal[0].'`+
                    `&sucursal=`+`Calle '. $info_sucursal[1] . ', Col.' . $info_sucursal[2] . ' C.P.: ' . $info_sucursal[3] . '`+
                    `&nombre_usuario=`+`'.$_SESSION['userName'].'`+
                    `&fecha=`+`'.date("d/m/Y").'`+
                    `&folio=`+String(idVenta)+
                    `&total=`+String(global_total)+
                    `&iva=`+String(global_iva)
                    )

                    Swal.fire({
                      icon: "success",
                      title: "Descargando ticket :)",
                      showConfirmButton: false,
                      timerProgressBar: true,
                      timer: 1500,
                      willClose: () => { location.reload()   } 
                    })

                  } else if (result.isDenied) {
                    Swal.fire({
                      icon: "info",
                      title: "No se imprimira el ticket",
                      showConfirmButton: false,
                      timerProgressBar: true,
                      timer: 1500,
                      willClose: () => { 
                        fetch("./services/doNotDownloadTicket.php", {
                          method: "GET",
                        }).then(
                            response => response.json()
                        ).then(
                            response => location.reload()
                        ).catch(
                            error => console.log(error)
                        ) 
                      
                      
                      } 
                    })
                    


                  }
                })
              })
              
            </script>
            ');
          }
        }
    ?>
    <?php else: ?>
    <h4>No hay un punto asignado a este vendedor</h4>
    <?php endif; ?>
     