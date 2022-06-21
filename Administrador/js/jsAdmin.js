
function alertElimarUsuario(id){
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
          window.location.href = "index.php?configuracion=true&id="+id;
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

function alertAgregarPunto(){
  Swal.fire(
      'Punto de venta agregado',
      'Se ha agregado satisfactoriamente un nuevo punto de venta',
      'success',
    )
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

function alertUpdateUser(form){
  Swal.fire({
    icon: 'question',
    title: 'Actualización de usuario',
    text: '¿Estas seguro de los cambios a realizar?',
  }).then((result) => {
    if (result.isConfirmed) {
      form.submit();
    }  
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

function alertEdicionSatisfactoria(){ 
  Swal.fire({
    icon: 'success',
    title: 'Edición de producto',
    text: 'Se ha realizado la edición del producto satisfactoriamente',
    showConfirmButton: false,
    timer: 3000
  })
}

function alertEdicionSatisfactoriaUsuario(){ 
  Swal.fire({
    icon: 'success',
    title: 'Edición de usuario',
    text: 'Se ha realizado la edición de los datos de usuario satisfactoriamente',
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
      ).then((result) => {
        if (result.isConfirmed) {
          location.reload()
        }  
      })
}

function alertConfigImpuestoError(){
  Swal.fire(
      'Error',
      'No se pudo actualizar la region',
      'error',
    )
}

function actualizarProductoInventarioExito(){
  Swal.fire(
      'Producto actualizado',
      'Se ha actualizado exitosamente el producto',
      'success',
    )
}

function actualizarProductoInventarioError(msg){
  Swal.fire(
      'Error',
      msg,
      'error',
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