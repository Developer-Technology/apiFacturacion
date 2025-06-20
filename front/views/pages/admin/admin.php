<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
token para consumo
=============================================*/
$token = TemplateController::tokenSet();
$current_period = date('m-Y');

/*=============================================
Contamos las empresas
=============================================*/
$urlT = "empresas";
$methodT = "GET";
$fieldsT = array();

$responseT = CurlController::requestSunat($urlT, $methodT, $fieldsT, $token);

if ($responseT->response->success == true) {

    $totalT = $responseT->response->total;

} else {

    $totalT = 0;

}

/*=============================================
Sumamos los consumos
=============================================*/
$urlTc = "empresas";
$methodTc = "GET";
$fieldsTc = array();

$responseTc = CurlController::requestSunat($urlTc, $methodTc, $fieldsTc, $token);

$totalConsultas = 0;
$totalDocumentos = 0;

if($responseTc->response->status == 200) {

    foreach ($responseTc->response->data as $empresaTc) {

        if ($empresaTc->id_empresa != 1) {
    
            $jsonConsultas = json_decode($empresaTc->consumo_empresa);
    
            foreach ($jsonConsultas as $itemCons) {
    
                if ($itemCons->periodo === $current_period) {
    
                    $totalConsultas += intval($itemCons->consultas);
                    $totalDocumentos += intval($itemCons->documentos);
    
                }
    
            }
            
        }
    
    }

}

/*=============================================
Porcentajes de consumos
=============================================*/
if ($totalConsultas > $totalDocumentos) {

    $percentCons = round((($totalDocumentos * 100) / $totalConsultas));

} else if ($totalDocumentos > $totalConsultas) {

    $percentCons = round((($totalConsultas * 100) / $totalDocumentos));

} else {

    $percentCons = 0;

}

/*=============================================
Contamos los planes
=============================================*/
$urlP = "planes";
$methodP = "GET";
$fieldsP = array();

$responseP = CurlController::requestSunat($urlP, $methodP, $fieldsP, $token);

if ($responseP->response->success == true) {

    $totalP = $responseP->response->total;

} else {

    $totalP = 0;

}

/*=============================================
Contamos el total de usuarios
=============================================*/
$urlUt = "usuarios";
$methodUt = "GET";
$fieldsUt = array();

$responseUt = CurlController::requestSunat($urlUt, $methodUt, $fieldsUt, $token);

if ($responseUt->response->success == true) {

    $totalUt = $responseUt->response->total;

} else {

    $totalUt = 0;

}

/*=============================================
Contamos el total de usuarios verificados
=============================================*/
$urlUv = "usuarios?select=*&linkTo=verificado_usuario&equalTo=1";
$methodUv = "GET";
$fieldsUv = array();

$responseUv = CurlController::requestSunat($urlUv, $methodUv, $fieldsUv, $token);

if ($responseUv->response->success == true) {

    $totalUv = $responseUv->response->total;

} else {

    $totalUv = 0;

}

/*=============================================
Contamos el total de usuarios no verificados
=============================================*/
$urlUr = "usuarios?select=*&linkTo=verificado_usuario&equalTo=0";
$methodUr = "GET";
$fieldsUr = array();

$responseUr = CurlController::requestSunat($urlUr, $methodUr, $fieldsUr, $token);

if ($responseUr->response->success == true) {

    $totalUn = $responseUr->response->total;

} else {

    $totalUn = 0;

}

/*=============================================
Contamos el total de usuarios registrados en el dia
=============================================*/
$urlUn = "usuarios?select=*&linkTo=creado_usuario&equalTo=" . date("Y-m-d");
$methodUn = "GET";
$fieldsUn = array();

$responseUn = CurlController::requestSunat($urlUn, $methodUn, $fieldsUn, $token);

if ($responseUn->response->success == true) {

    $totalUr = $responseUn->response->total;

} else {

    $totalUr = 0;

}

/*=============================================
Obtenemos datos para el grafico
=============================================*/
// Generar un arreglo con los nombres de los meses en español
$meses = [
    'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
    'Jul', 'Ago', 'Sept', 'Oct', 'Nov', 'Dic',
];

// Generar un arreglo con los datos de consultas y documentos
$consultasPorMes = array_fill(0, 12, 0);
$documentosPorMes = array_fill(0, 12, 0);

// Sumar los valores de consultas y documentos por mes y por periodo
if($responseTc->response->status == 200) {

    foreach ($responseTc->response->data as $empresaTc) {

        if($empresaTc->id_empresa != 1) {
    
            // Convertir el JSON a un array de PHP
            $jsonChart = $empresaTc->consumo_empresa;
            $data = json_decode($jsonChart, true);
    
            foreach ($data as $item) {
                $periodoParts = explode('-', $item['periodo']);
                $mes = intval($periodoParts[0]) - 1;
                $consultasPorMes[$mes] += intval($item['consultas']);
                $documentosPorMes[$mes] += intval($item['documentos']);
            }
    
        }
        
    }
    
}

?>

<div class="content__header content__boxed overlapping">
    <div class="content__wrap">

        <!-- Page title and information -->
        <h1 class="page-title mb-2">Dashboard</h1>
        <h2 class="h5"><?php echo "$vector[$numero]"; ?></h2>
        <p class="lead"></p>
        <!-- END : Page title and information -->

    </div>

</div>

<div class="content__boxed">
    <div class="content__wrap">
        <div class="row">
            <div class="col-xl-7 mb-3 mb-xl-0">

                <div class="card h-100">
                    <div class="card-header d-flex align-items-center border-0">
                        <div class="me-auto">
                            <h3 class="h4 m-0">Gráfico Lineal <?php echo date('Y') ?></h3>
                        </div>
                    </div>

                    <!-- Network - Area Chart -->
                    <div class="card-body py-0" style="height: auto;">
                        <canvas id="myChart"></canvas>
                    </div>
                    <!-- END : Network - Area Chart -->
                </div>
            </div>
            <div class="col-xl-5">
                <div class="row">
                    <div class="col-sm-6">

                        <!-- Tile - HDD Usage -->
                        <div class="card bg-success text-white overflow-hidden mb-3">
                            <div class="p-3 pb-2">
                                <h5 class="mb-3">
                                    <i class="demo-psi-data-storage text-reset text-opacity-75 fs-3 me-2"></i> Espacio En Disco Duro
                                </h5>
                                <ul class="list-group list-group-borderless">
                                    <li
                                        class="list-group-item p-0 text-reset d-flex justify-content-between align-items-start">
                                        <div class="me-auto">Disponible</div>
                                        <span class="fw-bold"><?php print("$freeDisk GB");?></span>
                                    </li>
                                    <li
                                        class="list-group-item p-0 text-reset d-flex justify-content-between align-items-start">
                                        <div class="me-auto">Utilizado</div>
                                        <span class="fw-bold"><?php print("$utilizadoDisk GB");?></span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Area Chart -->
                            <div class="py-0 p-3" style="height: 70px;">
                                <div class="progress progress-md mb-2 mt-3">
                                    <div class="progress-bar bg-white" role="progressbar"
                                        style="width: <?php echo $perncentDiskUtil; ?>%;" aria-valuenow="<?php echo $perncentDiskUtil; ?>" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>

                                <p class="text-white text-opacity-75 mb-0"><strong><?php echo $perncentDiskFree; ?>%</strong> Disponible de <?php print("$totalDisk GB");?> </p>
                            </div>
                            <!-- END : Area Chart -->

                        </div>
                        <!-- END : Tile - HDD Usage -->

                    </div>
                    <div class="col-sm-6">

                        <!-- Tile - Earnings -->
                        <div class="card bg-info text-white overflow-hidden mb-3">
                            <div class="p-3 pb-2">
                                <h5 class="mb-3">
                                    <i class="demo-psi-coin text-reset text-opacity-75 fs-2 me-2"></i> Consumos <?php echo date('Y') ?>
                                </h5>
                                <ul class="list-group list-group-borderless">
                                    <li
                                        class="list-group-item p-0 text-reset d-flex justify-content-between align-items-start">
                                        <div class="me-auto">Consultas</div>
                                        <span class="fw-bold"><?php echo $totalConsultas ?></span>
                                    </li>
                                    <li
                                        class="list-group-item p-0 text-reset d-flex justify-content-between align-items-start">
                                        <div class="me-auto">Documentos</div>
                                        <span class="fw-bold"><?php echo $totalDocumentos ?></span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Line Chart -->
                            <div class="py-0 p-3" style="height: 70px;">
                                <div class="progress progress-md mb-2 mt-3">
                                    <div class="progress-bar bg-white" role="progressbar"
                                        style="width: <?php echo $percentCons; ?>%;" aria-valuenow="<?php echo $percentCons; ?>" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>

                                <p class="text-white text-opacity-75 mb-0">Direfencia del <strong><?php echo $percentCons; ?>%</strong> en consumos </p>
                            </div>
                            <!-- END : Line Chart -->

                        </div>
                        <!-- END : Tile - Earnings -->

                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">

                        <!-- Tile - Sales -->
                        <div class="card mb-3 mb-xl-3">
                            <div class="card-body py-3">

                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="img-md ratio ratio-1x1 bg-purple text-white rounded-circle">
                                            <i
                                                class="d-flex align-items-center justify-content-center fa fa-building-o fs-2"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="h2 mb-0"><?php echo $totalT ?></h5>
                                        <p class="mb-0">Empresas</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- END : Tile - Sales -->

                    </div>
                    <div class="col-sm-6">

                        <!-- Tile - Task Progress -->
                        <div class="card mb-3 mb-xl-3">
                            <div class="card-body py-3">

                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="img-md ratio ratio-1x1 bg-warning text-white rounded-circle">
                                            <i class="d-flex align-items-center justify-content-center fa fa-list fs-2"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="h2 mb-0"><?php echo $totalP ?></h5>
                                        <p class="mb-0">Planes</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- END : Tile - Task Progress -->

                    </div>
                </div>

                <!-- Simple state widget -->
                <div class="card">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 p-3">
                                <div class="h3 display-3"><?php echo $totalUr ?></div>
                                <span class="h6">Nuevos Usuarios (hoy)</span>
                            </div>
                            <div class="flex-grow-1 text-center ms-3">
                                <a href="/users" class="btn btn-sm btn-danger">Ver Usuarios</a>

                                <!-- Social media statistics -->
                                <div class="mt-4 pt-3 d-flex justify-content-around border-top">
                                    <div class="text-center">
                                        <h4 class="mb-1"><?php echo $totalUt ?></h4>
                                        <small class="text-muted">Total</small>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="mb-1"><?php echo $totalUv ?></h4>
                                        <small class="text-muted">Verificado</small>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="mb-1"><?php echo $totalUn ?></h4>
                                        <small class="text-muted">Sin Verificar</small>
                                    </div>
                                </div>
                                <!-- END : Social media statistics -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END : Simple state widget -->

            </div>
        </div>

    </div>
</div>

<script>
    var ctx = document.getElementById('myChart').getContext('2d');

    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [{
            label: 'Consultas',
            data: <?php echo json_encode($consultasPorMes); ?>,
            backgroundColor: 'rgba(37,71,106,0.2)',
            borderColor: 'rgba(37,71,106,1)',
            borderWidth: 2
            }, {
            label: 'Documentos',
            data: <?php echo json_encode($documentosPorMes); ?>,
            backgroundColor: 'rgba(3,169,244,0.2)',
            borderColor: 'rgba(3,169,244,1)',
            borderWidth: 2
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                    beginAtZero: true,
                    min: 0 // Establecer el valor mínimo en cero
                    }
                }]
            }
        }
    });
</script>