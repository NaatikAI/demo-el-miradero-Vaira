<div class="sidenav">
    <a href="?recibos=true"<?php if (isset($_GET['recibos'])) { ?> class="current" <?php } ?>><i class="fa fa-ticket"></i> Recibos</a>
    <a href="?reportes=true"<?php if (isset($_GET['reportes'])) { ?> class="current" <?php } ?>><i class="fa fa-line-chart"></i> Reportes</a>
    <a href="?inventario=true"<?php if (isset($_GET['inventario'])) { ?> class="current" <?php } ?>><i class="fa fa-archive"></i> Inventario</a>
    <a href="?configuracion=true"<?php if (isset($_GET['configuracion'])) { ?> class="current" <?php } ?>><i class="fa fa-cogs"></i> Configuración</a>
    <a href="?ayuda_y_soporte=true"<?php if (isset($_GET['ayuda_y_soporte'])) { ?> class="current" <?php } ?>><i class="fa fa-info-circle"></i> Ayuda y soporte</a>
</div>