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
            $this->Cell(0,0,'Nombre del negocio',0,0,'C');  // Nombre del negocio
            $this->Ln(10);
            $this->SetFont('Arial','',11);
            $this->Cell(0,0,'Sucursal',0,0,'C');  // Direccion
            $this->Ln(7);
            $this->Cell(0,0,'________________________________________________________',0,0,'C');  // Vendedor
            $this->Ln(7);
            $this->Cell(0,0,'CORTE DE CAJA',0,0,'C');  // Vendedor
            $this->Ln(4);
            $this->Cell(0,0,'________________________________________________________',0,0,'C');  // Vendedor
            $this->Ln(12);
            $this->Cell(0,0,'Punto de venta:  ',0,0,'L');  // Vendedor
            $this->Ln(6);
            $this->Cell(0,0,'Fecha:  ',0,0,'L');  // Vendedor
            
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
    // $db = new dbConexion();
    // $connString = $db->getConexion();
    // $display_heading = array('idp'=>'ID', 'personal_nombre'=> 'Nombre', 'personal_edad'=> 'Edad','personal_salario'=> 'Salario','fecha'=> 'Fecha',);

    // $result = mysqli_query($connString, "SELECT idp, personal_nombre, personal_edad, personal_salario, fecha FROM personal") or die("database error:". mysqli_error($connString));
    // $header = mysqli_query($connString, "SHOW columns FROM personal");
    $result = Array(
        "object1"=> Array(   
            "attr1" => "1", 
            "attr2" => "name of article", 
            "attr3" => 1200, 
            "attr4" => 1200,
            "attr5" => 1200
        ),
        "object2"=> Array(   
            "attr1" => "2", 
            "attr2" => "name of article", 
            "attr3" => 2400, 
            "attr4" => 2400,
            "attr5" => 2400
        )
    );
    $pdf = new PDF();
    //header
    $pdf->AddPage();
    //foter page
    $pdf->AliasNbPages();
    $pdf->SetFont('Arial','B',12);
    $w = array(40, 50, 40, 30,30); // Declaramos el ancho de las columnas
    //Declaramos el encabezado de la tabla
    $pdf->Cell($w[0],6,'CANTIDAD', true,0,'C');
    $pdf->Cell($w[1],6,'PRODUCTO',true,0,'C');
    $pdf->Cell($w[2],6,'IMPORTE',true,0,'C');
    $pdf->Cell($w[3],6,'SUBTOTAL',true,0,'C');
    $pdf->Cell($w[4],6,'TOTAL',true,0,'C');
    $pdf->Ln(6);
    $pdf->SetFont('Arial','',12);
    //Mostramos el contenido de la tabla
    foreach($result as $row){
        $pdf->Cell($w[0],6,$row['attr1'],1);
        $pdf->Cell($w[1],6,$row['attr2'],1);
        $pdf->Cell($w[2],6,number_format($row['attr4']),1);
        $pdf->Cell($w[3],6,number_format($row['attr4']),1);
        $pdf->Cell($w[4],6,number_format($row['attr4']),1);
        $pdf->Ln();
    }

    $pdf->Ln(10);
    $pdf->SetFont('Arial','',11);
    $pdf->Cell(0,0,'Subtotal                $ 10',0,0,'R');  // Nombre del negocio
    $pdf->Ln(6);
    $pdf->Cell(0,0,'Subtotal I.V.A.                $ 10',0,0,'R');  // Nombre del negocio
    $pdf->Ln(6);
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,0,'Total                $ 10',0,0,'R');  // Nombre del negocio
    


    // $pdf->Output('D','ticket.pdf');
    $pdf->Output();

?>