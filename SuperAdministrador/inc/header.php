<div class="row" >
    <nav class="navbar">
    <a class="navbar-brand" href="index.php?nueva_venta=true">
        <img src="../src/image/vairaNav.png"  width="50" height="50" style="margin-left: 8px;margin-top: -5px" class="d-inline-block align-top" alt="">
    </a>
    <form class="d-flex"  action="../services/cerrar.php">
        <label for="usuariologueado" style="margin-right: 10px; margin-top:5px">Usuario: <?php echo $_SESSION['userName'] ?></label>
        <button class="btn btn-primary cerrarSesionBtn" type="submit"><i class="fa fa-sign-out" aria-hidden="true"></i></button>
    </form>
    </nav>
</div>