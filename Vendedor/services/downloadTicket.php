<?php
    //Incluimos el fichero de conexion
    // include_once("dbconect.php");
    //Incluimos la libreria PDF
    include_once('fpdf.php');

    class PDF extends FPDF
    {
        function Header()
        {
            // Logo
            // $this->Image('../../src/image/vaira.png',10,-1,50);
            $this->Ln(10);
            $this->SetFont('Arial','B',18);
            $this->Cell(0,0, $_GET['nombre_tienda'] ,0,0,'C');  // Nombre del negocio
            $this->Ln(10);
            $this->SetFont('Arial','',11);
            $this->Cell(0,0,'Direccion: '.$_GET['sucursal'],0,0,'C');  // Sucursal
            $this->Ln(7);
            $this->Cell(0,0,'Vendedor: '.$_GET['nombre_usuario'],0,0,'C');  // Vendedor
            $this->Ln(7);
            $this->Cell(0,0,'Fecha: '.$_GET['fecha'].'                                    Folio: '.$_GET['folio'],0,0,'C');  // Fecha y folio
            $this->Ln(7);
            $this->Cell(0,0,'________________________________________________________',0,0,'C');  // Vendedor
            $this->Ln(7);
            $this->Cell(0,0,'TICKET DE COMPRA',0,0,'C');  // Vendedor
            $this->Ln(4);
            $this->Cell(0,0,'________________________________________________________',0,0,'C');  // Vendedor
            
            // Line break
            $this->Ln(10);
        }
        function Footer()
        {
            // Position at 1.5 cm from bottom
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial','I',8);
            // Page number
            $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
        }
    }
   
    $pdf = new PDF();
    //header
    $pdf->AddPage();
    //foter page
    $pdf->AliasNbPages();
    $pdf->SetFont('Arial','B',12);
    $w = array(40, 80, 40, 30); // Declaramos el ancho de las columnas
    //Declaramos el encabezado de la tabla
    $pdf->Cell($w[0],6,'SKU', 0,0,'C');
    $pdf->Cell($w[1],6,'ARTICULO',0,0,'C');
    $pdf->Cell($w[2],6,'CANTIDAD',0,0,'C');
    $pdf->Cell($w[3],6,'PRECIO',0,0,'C');
    $pdf->Ln(6);
    $pdf->SetFont('Arial','',12);
    //Mostramos el contenido de la tabla
    $received = $_GET['productos'];
    $received =  json_decode($received, true);
    foreach($received as $row){
        $pdf->Cell($w[0],6,$row['sku_producto'],0, 0, 'C');
        $pdf->Cell($w[1],6,$row['nombre_producto'],0, 0, 'C');
        $pdf->Cell($w[2],6,$row['cantidad'],0, 0, 'C');
        $pdf->Cell($w[3],6,number_format($row['precio_unitario'] * $row['cantidad'],2),0, 0, 'C');
        $pdf->Ln();
    }

    $pdf->Ln(10);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,0,'Total M.N.                $ '.$_GET['total'],0,0,'C');  // Nombre del negocio
    $pdf->Ln(6);
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(0,0,'I.V.A. 16%                $ '.$_GET['iva'],0,0,'C');  // Nombre del negocio
    $pdf->Ln(6);
    $pdf->SetFont('Arial','',11);
    $pdf->Cell(0,0,'¡Gracias por su compra!',0,0,'C');  // Nombre del negocio
    
    session_start();
    unset($_SESSION["cart"][$_SESSION['id_punto_de_venta']]);

    $pdf->Output('D','ticket.pdf');

    // $pdf->Output();

?>