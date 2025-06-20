<?php

/*-------------------------
Autor: Developer Technology
Web: www.developer-technology.net
Mail: info@developer-technology.net
---------------------------*/

/*=============================================
Decodificamos los datos de la empresa
=============================================*/
$emisor = json_decode($emisor);

/*=============================================
Definimos el nombre de la moneda
=============================================*/
if ($comprobante->tipoMoneda == 'PEN') {
    $tipoMoneda = 'S/ ';
    $nombreMoneda = 'SOLES';
} else {
    $tipoMoneda = '$USD ';
    $nombreMoneda = 'DÓLARES AMERICANOS';
}

/*=============================================
Definimos el nombre del documento
=============================================*/
if ($comprobante->tipoDoc == '01') {
    $nombre_comprobante = 'FACTURA ELECTRÓNICA';
} else if ($comprobante->tipoDoc == '03') {
    $nombre_comprobante = 'BOLETA DE VENTA ELECTRÓNICA';
} else if ($comprobante->tipoDoc == 'nt' || $comprobante->tipoDoc == '99') {
    $nombre_comprobante = 'TICKET DE VENTA';
} else if ($comprobante->tipoDoc == 'cz' || $comprobante->tipoDoc == '97') {
    $nombre_comprobante = 'COTIZACIÓN';
} else {
    $nombre_comprobante = '----';
}

if ($comprobante->tipoDoc == '02') {
    $nombre_comprobante = $tipo_comprobante['descripcion'];
}

if ($comprobante->tipoDoc == '00') {
    $nombre_comprobante = $tipo_comprobante['descripcion'];
}

/* Convertimos el total a texto */
$decimales = explode(".", number_format($comprobante->total, 2));
$entera = explode(".", $comprobante->total);
$totalTexto = ControladorRutas::convertir($entera[0]) . ' CON ' . $decimales[1] . '/100 SOLES';

?>

<style>
    .anulado-print {
        position: absolute;
        width: 100%;
        height: 100%;
        font-size: 25px;
        color: red;
        z-index: 1;
        text-align: center;
        font-weight: bold;
    }

    .anulado-print span {
        font-size: 16px;
        color: red;
    }

    .logo {
        text-align: center;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .logo img {
        width: 40mm;
    }

    .empresa {
        text-align: center;
    }

    .empresa h3 {
        font-size: 14px;
        margin: 0;
        padding: 0;
        color: #3d4b43;
    }

    .empresa h3 span {
        font-size: 11px;
        font-weight: normal;
    }

    .numero-ruc {
        text-align: center;
    }

    .numero-ruc h3 {
        font-size: 14px;
        margin: 0;
        padding: 0;
        color: #3d4b43;
    }

    .tabledesc {
        width: 77.5mm;
        border-collapse: collapse;
    }

    .tabledesc .border {
        margin: 0mm;
        padding: 0mm;
        
    }

    .tabledesc tr {
        width: 77.5mm;
        /* border: 0.5px solid #333; */
    }

    .tabledesc tr th {
        font-size: 14px;
        padding: 0.2mm;
        margin: 0;
        padding-bottom: 0mm;
        color: #3d4b43;
        background: #d3d3d3;
        border: 0.3px solid #333;
    }

    .tabledesc tr td {
        font-size: 12px;
        padding: 0.2mm;
        margin: 0;
        border: 0.2px solid #33333398;
        
    }
    .b-l{
        border-radius: 7px 0px 0px 0px;
    }

    .b-r{
        border-radius: 0px 7px 0px 0px;
    }

    .conte-detalles {
        margin-top: 1mm;
    }

    .table-totales {
        width: 100%;
        margin-top: 1mm;
    }

    .table-totales tr td {
        padding: 0.4mm;
        margin: 0.4mm;
        font-size: 13px;
        padding-bottom: 1mm;
    }

    .table-cliente {
        width: 100%;
    }

    .table-cliente tr td {
        font-size: 12px;
        padding: 0.2mm;
        margin: 0;
    }

    .bar-code {
        margin-top: 1.5mm;
        text-align: center;
    }

    .en-letras {
        font-size: 12px;
    }

    .en-letras span {
        font-size: 12px;
    }

    .impresa {
        text-align: center;
        margin-top: 1.5mm;
        font-size: 9px;
    }

    .anulado-print {
        position: absolute;
        top: 13%;
        left: 2%;
        color: red;
        font-size: 20px;
        text-align: center;
        font-weight: bold;
    }

    .anulado-print span {
        color: red;
    }

    .v5 {
        width: 5%;
    }

    .v10 {
        width: 10%;
    }

    .v15 {
        width: 15%;
    }

    .v20 {
        width: 20%;
    }

    .v25 {
        width: 25%;
    }

    .v30 {
        width: 30%;
    }

    .v35 {
        width: 35%;
    }

    .v40 {
        width: 40%;
    }

    .v45 {
        width: 45%;
    }

    .v50 {
        width: 50%;
    }

    .v55 {
        width: 55%;
    }

    .v60 {
        width: 60%;
    }

    .v65 {
        width: 65%;
    }

    .v70 {
        width: 70%;
    }

    .v75 {
        width: 75%;
    }

    .v80 {
        width: 80%;
    }

    .v100 {
        width: 100%;
    }

    hr {
        border: 0;
        border-top: 0.5px solid black;
    }
</style>

<page backtop="1.5mm" backbottom="1.5mm" backleft="1.5mm" backright="1.5mm">
    <page_header></page_header>
    <page_footer></page_footer>

    <div class="logo">
        <?php
        if ($emisor->logo == '') {
            echo '<br><br>';
        } else {
            echo '<img src="documents/logo/' . $emisor->ruc . '/' . $emisor->logo . '" alt="Logo"><br>';
        }
        ?>
    </div>

    <div class="empresa">
        <b><h3><?php echo $emisor->razonSocial; ?></h3></b>
        <span><?php echo $emisor->address->direccion; ?></span>
    </div>

    <br>

    <div>
        <span><?php echo $emisor->nombreComercial; ?></span>
        <br>
        <span>Telf: <?php echo $emisor->telefono; ?></span>
        <br>
        <span>Mail: <?php echo $emisor->email; ?></span>
    </div>

    <br>
    <hr>

    <div class="numero-ruc">
        <h3>RUC <?php echo $emisor->ruc; ?></h3>
    </div>

    <hr>

    <div class="numero-ruc">
        <h3><?php echo $nombre_comprobante; ?></h3>
        <h3><?php echo $comprobante->serie . ' - ' . str_pad($comprobante->correlativo, 8, "0", STR_PAD_LEFT); ?></h3>
    </div>

    <hr>

    <div class="conte-cliente">
        <table class="table-cliente">
            <tr>
                <td style="width:15mm; text-align:left; border-radius: 7px 0px 0px 0px;"><b>CLIENTE: </b></td>
                <td style="width:56mm; text-align:left;"><?php echo $cliente->rznSocial ?></td>
            </tr>
            <tr>
                <td style="width:15mm; text-align:left;"><b>DOCUM.: </b></td>
                <td style="width:56mm; text-align:left;"><?php echo $cliente->numDoc ?></td>
            </tr>
            <tr>
                <td style="width:15mm; text-align:left;"><b>DIRECC.: </b></td>
                <td style="width:56mm; text-align:left;"><?php echo $cliente->direccion ?></td>
            </tr>
            <tr>
                <td style="width:15mm; text-align:left;"><b>EMISIÓN: </b></td>
                <td style="width:56mm; text-align:left;"><?php echo date("d/m/Y", strtotime($comprobante->fechaEmision)); ?></td>
            </tr>
            <tr>
                <td style="width:15mm; text-align:left;"><b>PAGO: </b></td>
                <td style="width:56mm; text-align:left;"><?php echo $comprobante->tipoPago ?></td>
            </tr>
            <tr>
                <td style="width:15mm; text-align:left; border-radius: 0px 7px 0px 0px;"><b>MONEDA: </b></td>
                <td style="width:56mm; text-align:left;"><?php echo $nombreMoneda ?></td>
            </tr>
        </table>
    </div>

    <br>
    <hr>

    <div style="font-size: 12px;">
        <b>OBSERVACIÓN: </b><?php echo $comprobante->observacion; ?>
    </div>

    <hr>

    <div class="conte-detalles">
        <table class="tabledesc">
            <thead class="header">
                <tr class="tr-head">
                    <th class="b-l" style="text-align:center; width:10mm;">Cant.</th>
                    <th style="text-align:center; width:30mm;">Descripción</th>
                    <th style="text-align:center; width:13mm;">P. Unit</th>
                    <th class="b-r" style="text-align:center; width:13mm;">Importe</th>
                </tr>
                <tr>
                    <!-- <th colspan="4" class="border"></th> -->
                </tr>
            </thead>
            <tbody>
                <?php
                $descuentosItems = 0.00;
                foreach ($detalle as $key => $fila) {
                    $importe_total = ($fila->mtoValorUnitario > 0) ? $fila->mtoPrecioUnitario : number_format(0.00, 2);
                ?>
                    <tr>
                        <td style="width:10mm; text-align:center;"><?php echo $fila->cantidad; ?></td>
                        <td style="width:30mm; text-align:center;"><?php echo $fila->descripcion; ?></td>
                        <td style="width:14mm;"><?php echo $tipoMoneda . ' ' . number_format($fila->mtoValorUnitario, 2); ?></td>
                        <td style="width:14mm;"><?php echo $tipoMoneda . ' ' . number_format($importe_total, 2); ?></td>
                    </tr>
                <?php
                    if (isset($fila->descuentos->monto)) {
                        $descuentosItems += $fila->descuentos->monto;
                    }
                }
                $descuentosItems;
                if (isset($comprobante->dsctoGlobal->descuento)) {
                    $descuentos = round($comprobante->dsctoGlobal->descuento + $descuentosItems, 2);
                } else {
                    $descuentos = 0.00;
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- <hr> -->

    <div class="conte-totales">
        <?php
        /* Gravadas */
        if (isset($comprobante->mtoOperGravadas)) {
            $gravadas = $comprobante->mtoOperGravadas;
        } else {
            $gravadas = 0;
        }

        /* Exoneradas */
        if (isset($comprobante->mtoOperExoneradas)) {
            $exoneradas = $comprobante->mtoOperExoneradas;
        } else {
            $exoneradas = 0;
        }

        /* Inafectas */
        if (isset($comprobante->mtoOperInafectas)) {
            $inafectas = $comprobante->mtoOperInafectas;
        } else {
            $inafectas = 0;
        }

        /* Gratuitas */
        if (isset($comprobante->mtoOperGratuitas)) {
            $gratuitas = $comprobante->mtoOperGratuitas;
        } else {
            $gratuitas = 0;
        }

        /* Exportacion */
        if (isset($comprobante->mtoExportacion)) {
            $exportacion = $comprobante->mtoExportacion;
        } else {
            $exportacion = 0;
        }

        /* Icbper */
        if (isset($comprobante->icbper)) {
            $icbper = $comprobante->icbper;
        } else {
            $icbper = 0;
        }
        ?>

        <table class="table-totales">
            <tr>
                <td style="width:49mm; text-align:right;">Op. Gravadas: </td>
                <td style="width:21mm; text-align:left"><?php echo $tipoMoneda . ' ' . number_format($gravadas, 2); ?></td>
            </tr>

            <?php if ($comprobante->tipoDoc == '01' || $comprobante->tipoDoc == '03') : ?>
                <tr>
                    <td style="width:49mm; text-align:right;">Op. Exoneradas: </td>
                    <td style="width:21mm; text-align:left"><?php echo $tipoMoneda . ' ' . number_format($exoneradas, 2); ?></td>
                </tr>
                <tr>
                    <td style="width:49mm; text-align:right;">Op. Inafectas: </td>
                    <td style="width:21mm; text-align:left"><?php echo $tipoMoneda . ' ' . number_format($inafectas, 2); ?></td>
                </tr>
                <tr>
                    <td style="width:49mm; text-align:right;">Op. Gratuitas: </td>
                    <td style="width:21mm; text-align:left"><?php echo $tipoMoneda . ' ' . number_format($gratuitas, 2); ?></td>
                </tr>
                <tr>
                    <td style="width:49mm; text-align:right;">Op. Exportación: </td>
                    <td style="width:21mm; text-align:left"><?php echo $tipoMoneda . ' ' . number_format($exportacion, 2); ?></td>
                </tr>
            <?php endif ?>

            <tr>
                <td style="width:49mm; text-align:right;">Descuentos(-): </td>
                <td style="width:21mm; text-align:left"><?php echo  $tipoMoneda . number_format($descuentos, 2) ?></td>
            </tr>

            <tr>
                <td style="width:49mm; text-align:right;">IGV(18%): </td>
                <td style="width:21mm; text-align:left"><?php echo  $tipoMoneda . ' ' . number_format($comprobante->mtoIGV, 2); ?></td>
            </tr>

            <?php if ($comprobante->tipoDoc == '01' || $comprobante->tipoDoc == '03') : ?>
                <tr>
                    <td style="width:49mm; text-align:right;">ICBPER: </td>
                    <td style="width:21mm; text-align:left"><?php echo  $tipoMoneda . number_format($icbper, 2); ?></td>
                </tr>
            <?php endif ?>

            <tr>
                <td style="width:49mm; text-align:right;">Total: </td>
                <td style="width:21mm; text-align:left"><?php echo  $tipoMoneda . ' ' . number_format($comprobante->total, 2); ?></td>
            </tr>
        </table>
    </div>

    <?php //if ($comprobante->tipoDoc == '01' || $comprobante->tipoDoc == '03') : ?>
        <div class="bar-code">
            <?php
            $ruc = $emisor->ruc;
            $tipo_documento = $comprobante->tipoDoc;
            $serie = $comprobante->serie;
            $correlativo = $comprobante->correlativo;
            $igv = $comprobante->mtoIGV;
            $total = $comprobante->total;
            $fecha = $comprobante->fechaEmision;
            $tipodoccliente = $cliente->tipoDoc;
            $nro_doc_cliente = $cliente->numDoc;
            ?>
            <img src="<?php echo 'documents/qr/' . $ruc . '/' .  $ruc . '-' . $tipo_documento . '-' . $serie . '-' . $correlativo . '.png'; ?>" style="width: 28mm; background-color: white; color: #000; border: none; padding:none">
        </div>
    <?php //endif ?>

    <br>
    <hr>

    <div class="en-letras">
        <span>SON <?php echo $totalTexto; ?></span>
    </div>

    <hr>

    <div class="en-letras">
        <span>GRACIAS POR SU PREFERENCIA</span>
        <?php if (isset($extra->usuario) && $extra->usuario != '') : ?>
            <br>
            <span>USUARIO: <?php echo $extra->usuario . " " . date("d/m/Y", strtotime($comprobante->fechaEmision)) . ' ' . date("H:i:s", strtotime($comprobante->horaEmision)); ?></span>
        <?php endif ?>
    </div>

    <hr>

    <div class="en-letras">
        <span>Representación impresa de la <?php echo $nombre_comprobante; ?></span>
        <?php if ($comprobante->tipoDoc == '01' || $comprobante->tipoDoc == '03') : ?>
            <br>
            <span>Autorizado mediante resolución N° 054-005-0001490/SUNAT</span>
        <?php endif ?>
        <?php if ($emisor->sistema != '') : ?>
            <br>
            <span>Generado gracias a <?php echo $emisor->sistema; ?></span>
        <?php endif ?>
    </div>

    <hr>

    <table style="font-size: 12px;">
        <tr>
            <td colspan="2">
                <b>Información Adicional</b>
            </td>
        </tr>
        <?php
        if ($comprobante->tipoPago == "Credito") {
        ?>
            <tr>
                <td class="v45">FORMA DE PAGO:</td>
                <td class="v55">Crédito</td>
            </tr>
            <?php
            $k = 1;
            foreach ($comprobante->cuotas as $keyC => $item) {
                $cuotas = $item->cuota;
                $fecha_cuota = $item->fechaCuota;
                echo '<tr>
                        <td class="v45">Fecha de pago</td>    
                        <td class="v55">Cuota00' . $k++ . '</td>    
                      </tr>';
                echo '<tr>
                        <td class="v45">' . $fecha_cuota . '</td>    
                        <td class="v55">' . number_format($cuotas, 2) . '</td>    
                      </tr>';
            }
        }
        ?>
    </table>

    <?php
    if ($comprobante->tipoDoc == '01' || $comprobante->tipoDoc == '03' || $comprobante->tipoDoc == 'nt' || $comprobante->tipoDoc == 'cz' || $comprobante->tipoDoc == '99') {
    ?>
        <div class="impresa">
            <?php
            if ($comprobante->tipoDoc == '01' || $comprobante->tipoDoc == '03') {
                if ($emisor->modo == 'beta') {
                    echo "<br><small><i>COMPROBANTE DE PRUEBA, NO TIENE NINGUNA VALIDEZ</i></small>";
                }

                if (isset($comprobante->bienesSelva) && $comprobante->bienesSelva == 'si') {
                    echo "<br>BIENES TRANSFERIDOS EN LA AMAZONIA REGION SELVA PARA SER CONSUMIDOS EN LA MISMA";
                }

                if (isset($comprobante->serviciosSelva) && $comprobante->serviciosSelva == 'si') {
                    echo "<br>SERVICIOS PRESTADOS EN LA AMAZONIA REGION SELVA PARA SER CONSUMIDOS EN LA MISMA";
                }
            }

            //Ingresamos el texto que no es un comprobante de pago electrónico valido
            if($comprobante->tipoDoc == '99') {

                echo "<br><br><small><small><i>ESTE NO ES UN COMPROBANTE ELECTRONICO VALIDO, SOLICITE UNA BOLETA O FACTURA</i></small></small>";

            }

            // Agregamos los comentarios o notas adicionales
            if (isset($extra->comentario) && $extra->comentario != '') {
                echo "<br>**<br>" . $extra->comentario;
            }

            // Agregamos la alerta que no es un comprobante de pago
            if ($comprobante->tipoDoc == 'nt') {
                echo "<br><br>**<i><small>Este no es un comprobante electrónico, reclama tu boleta o factura.</small></i>";
            }
            ?>
        </div>
    <?php
    }
    ?>
</page>
