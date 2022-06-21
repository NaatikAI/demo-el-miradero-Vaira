function alertEliminarUsuario(id){
  Swal.fire({
      title: 'Alerta',
      text: "¿Estás seguro que deseas eliminar este usuario?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si',
      cancelButtonText: 'Cancelar',
      allowOutsideClick: false
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "index.php?configuracion=true&id_usuario="+id;
      }  
    })
}

function alertCamposVacios(){
    Swal.fire(
        'Error',
        'Por favor llena todos los campos',
        'error',
      )
}
  
function alertUsuarioExistente(){
    Swal.fire(
        'Error',
        'El nombre de usuario ya existe, intenta con otro',
        'error',
      )
}
  
  function alertPassDiferente(){
    Swal.fire(
        'Error',
        'Las constraseñas no coinciden',
        'error',
      )
}

function alertEliminarProducto(id){
  Swal.fire({
      title: 'Alerta',
      text: "¿Estás seguro que deseas eliminar este producto?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si',
      cancelButtonText: 'Cancelar',
      allowOutsideClick: false
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "index.php?inventario=true&id="+id;
      }  
    })
}

function alertEliminarProveedor(id){
  Swal.fire({
      title: 'Alerta',
      text: "¿Estás seguro que deseas eliminar este proveedor?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si',
      cancelButtonText: 'Cancelar',
      allowOutsideClick: false
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "index.php?configuracion=true&id_proveedor="+id;
      }
    })
}

function alertEditarUsuario(){
  Swal.fire({
      title: 'Alerta',
      text: "¿Estás seguro que deseas editar este usuario?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si',
      cancelButtonText: 'Cancelar',
      allowOutsideClick: false
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire(
          'Usuario editado',
          'Se ha editado satisfactoriamente el usuario',
          'success',
        )
      }
    })
}

function alertEditarProveedor(){
  Swal.fire({
      title: 'Alerta',
      text: "¿Estás seguro que deseas editar este proveedor?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si',
      cancelButtonText: 'Cancelar',
      allowOutsideClick: false
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire(
          'Proveedor editado',
          'Se ha editado satisfactoriamente el proveedor',
          'success',
        )
      }
    })
}

function alertGeneraDocCorteCaja(){
    Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'Se ha generado el documento PDF con los datos de la fecha y sucursal que seleccionaste',
        showConfirmButton: false,
        timer: 4500
      })
}

function alertGeneraDocCorteCajaAdmin(){
  Swal.fire({
    position: 'top-end',
    icon: 'success',
    title: 'Se ha generado el documento PDF satisfactoriamente',
    showConfirmButton: false,
    timer: 1500
  })
}

function alertAgregarUsuario(){
    Swal.fire(
        'Usuario agregado',
        'Se ha agregado satisfactoriamente un nuevo usuario',
        'success',
      )
}

function alertAgregarProducto(){
    Swal.fire(
        'Producto agregado',
        'El producto se ha agregado satisfactoriamente',
        'success',
      )
}

function alertActualizarProducto(){
    Swal.fire(
        'Producto actualizado',
        'Se ha actualizado satisfactoriamente el producto',
        'success',
      )
}

function alertError(msg){
    Swal.fire(
        'Error',
        msg,
        'error',
      )
}

function alertSuccess(title, msg){
  Swal.fire(
      title,
      msg,
      'success',
    ).then((result) => {
      if (result.isConfirmed) {
        location.reload()
      }
    })
}

function alertErrorActualizarProducto(){
    Swal.fire(
        'Error',
        'No se ha podido actualizar el producto',
        'error',
      )
}

function alertAgregarProveedor(){
  Swal.fire(
      'Proveedor agregado',
      'Se ha agregado satisfactoriamente un nuevo proveedor',
      'success',
    )
}

function alertSucursalAsignada(){
  Swal.fire(
      'Sucursal asignada',
      'Se ha asignado exitosamente la sucursal',
      'success',
    ).then(() => {
      window.location.href = "index.php?configuracion=true";
    })
}

function alertDevolucion(){ 
  Swal.fire({
    icon: 'success',
    title: 'Devolución de compra',
    text: 'Se ha realizado la devolución satisfactoriamente',
    showConfirmButton: false,
    timer: 3000
  })
}

function alertFactura(){ 
  Swal.fire({
    icon: 'success',
    title: 'Facturación',
    text: 'Se han almacenado los datos de facturación satisfactoriamente',
    showConfirmButton: false,
    timer: 3000
  })
}

function alertConfigImpuesto(){
  Swal.fire(
      'Impuesto configurado',
      'Se ha configurado exitosamente el impuesto',
      'success',
    )
}

function myFunction() {
    var x = document.getElementById("password");
    if (x.type === "password") {
      x.type = "text";
    } else {
      x.type = "password";
    }
  } 

function mostrarPass() {
  var show = document.getElementById("showPass");
  if(show.checked == false){
    show.checked = true;
  }else{
    show.checked = false;  
  }

  var x = document.getElementById("password");
    if (x.type === "password") {
      x.type = "text";
    } else {
      x.type = "password";
    }
}