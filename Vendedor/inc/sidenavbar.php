<div class="sidenav">
    <a href="?nueva_venta=true"  <?php if (isset($_GET['nueva_venta'])) { ?> class="current" <?php } ?>><i class="fa fa-cart-arrow-down "></i> Nueva venta</a>
    <a href="?recibos=true"<?php if (isset($_GET['recibos'])) { ?> class="current" <?php } ?>><i class="fa fa-ticket"></i> Recibos</a>
    <a href="?reportes=true"<?php if (isset($_GET['reportes'])) { ?> class="current" <?php } ?>><i class="fa fa-line-chart"></i> Reportes</a>
    <a href="?ayuda_y_soporte=true"<?php if (isset($_GET['ayuda_y_soporte'])) { ?> class="current" <?php } ?>><i class="fa fa-info-circle"></i> Ayuda y soporte</a>
</div>