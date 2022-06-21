
function alertElimarUsuario(){
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
          Swal.fire(
            'Usuario eliminado',
            'Se ha eliminado satisfactoriamente el usuario',
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