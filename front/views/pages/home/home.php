<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

?>

<div class="content__header content__boxed overlapping">
    <div class="content__wrap">

        <!-- Page title and information -->
        <h1 class="page-title mb-2">Bienvenido</h1>
        <h2 class="h5"><?php echo $dataSett->nombre_sistema_configuracion ?>, un producto de <?php echo $dataSett->nombre_empresa_configuracion ?>

            <button class="btn btn-default border" data-toggle="modal" data-target="#viewToken" style="position: absolute; right: 20px; top: 12%;"><i class="fa fa-eye"></i> <span class="vr"></span> Mis Credenciales</button>

        </h2>
        <p class="lead"></p>
        <!-- END : Page title and information -->

    </div>

</div>

<div class="content__boxed pt-4">
    <div class="content__wrap">

        <!-- Tiles -->
        <div class="row">

            <div class="col-sm-6 col-lg-3">

                <!-- Stat widget -->
                <div class="card bg-cyan text-white mb-3 mb-xl-3">
                    <div class="card-body py-3 d-flex align-items-stretch">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                            <i class="fa fa-file fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="h2 mb-0"><?php echo $realDocs ?></h5>
                            <p class="mb-0">Documentos Emitidos</p>
                        </div>
                    </div>
                </div>
                <!-- END : Stat widget -->

            </div>

            <div class="col-sm-6 col-lg-3">

                <!-- Stat widget -->
                <div class="card bg-purple text-white mb-3 mb-xl-3">
                    <div class="card-body py-3 d-flex align-items-stretch">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                            <i class="fa fa-search fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="h2 mb-0"><?php echo $realCons ?></h5>
                            <p class="mb-0">Consultas Realizadas</p>
                        </div>
                    </div>
                </div>
                <!-- END : Stat widget -->

            </div>

            <div class="col-sm-6 col-lg-3">

                <!-- Stat widget -->
                <div class="card bg-orange text-white mb-3 mb-xl-3">
                    <div class="card-body py-3 d-flex align-items-stretch">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                            <i class="fa fa-credit-card fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="h2 mb-0"><?php echo $dataPlans->nombre_plan ?></h5>
                            <p class="mb-0">Plan Contratado</p>
                        </div>
                    </div>
                </div>
                <!-- END : Stat widget -->

            </div>

            <div class="col-sm-6 col-lg-3">

                <!-- Stat widget -->
                <div class="card bg-pink text-white mb-3 mb-xl-3">
                    <div class="card-body py-3 d-flex align-items-stretch">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                            <i class="fa fa-dollar fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="h2 mb-0"><?php echo $prxFact ?></h5>
                            <p class="mb-0">Próxima Facturación</p>
                        </div>
                    </div>
                </div>
                <!-- END : Stat widget -->

            </div>

            <div class="col-sm-6 col-lg-3">

                <!-- Stat widget -->
                <div class="card bg-pink text-white mb-3 mb-xl-3">
                    <div class="card-body py-3 d-flex align-items-stretch">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                            <i class="fa fa-bug fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="h2 mb-0"><?php echo TemplateController::capitalize($dataTenants->fase_empresa) ?></h5>
                            <p class="mb-0">Entorno</p>
                        </div>
                    </div>
                </div>
                <!-- END : Stat widget -->

            </div>

            <div class="col-sm-6 col-lg-3">

                <!-- Stat widget -->
                <div class="card bg-orange text-white mb-3 mb-xl-3">
                    <div class="card-body py-3 d-flex align-items-stretch">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                            <i class="fa fa-calendar fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="h2 mb-0"><?php echo $expCert ?></h5>
                            <p class="mb-0">Expira Certificado</p>
                        </div>
                    </div>
                </div>
                <!-- END : Stat widget -->

            </div>

            <div class="col-sm-6 col-lg-3">

                <!-- Stat widget -->
                <div class="card bg-purple text-white mb-3 mb-xl-3">
                    <div class="card-body py-3 d-flex align-items-stretch">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                            <i class="fa fa-search fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="h2 mb-0"><?php echo $dispCons ?></h5>
                            <p class="mb-0">Consultas Disponibles</p>
                        </div>
                    </div>
                </div>
                <!-- END : Stat widget -->

            </div>

            <div class="col-sm-6 col-lg-3">

                <!-- Stat widget -->
                <div class="card bg-cyan text-white mb-3 mb-xl-3">
                    <div class="card-body py-3 d-flex align-items-stretch">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-start">
                            <i class="fa fa-file fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="h2 mb-0"><?php echo $dispDocs ?></h5>
                            <p class="mb-0">Documentos Disponibles</p>
                        </div>
                    </div>
                </div>
                <!-- END : Stat widget -->

            </div>

            <div class="col-xl-12 mb-3 mb-xl-0">

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

        </div>
        <!-- END : Tiles -->

    </div>
</div>

<script>
    // Convertimos el JSON a un array de JavaScript
    const json = '<?php echo $dataTenants->consumo_empresa ?>';
    const data = JSON.parse(json);

    // Generamos un arreglo con los nombres de los meses en español
    const meses = [
      'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 
      'Jul', 'Ago', 'Sept', 'Oct', 'Nov', 'Dic'
    ];

    // Generamos un arreglo con los datos de consultas y documentos
    const consultas = Array.from({ length: 12 }).fill(0);
    const documentos = Array.from({ length: 12 }).fill(0);

    // Obtener el año actual
    const currentYear = new Date().getFullYear();

    // Procesar los datos para el año actual
    data.forEach(item => {
        const parts = item.periodo.split('-');
        const month = parseInt(parts[0]) - 1;
        const year = parseInt(parts[1]);
        if (year === currentYear) {
            consultas[month] += item.consultas;
            documentos[month] += item.documentos;
        }
    });

    // Creamos un nuevo gráfico Chart.js
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: meses,
        datasets: [{
          label: 'Consultas',
          data: consultas,
          backgroundColor: 'rgba(37,71,106,0.2)',
          borderColor: 'rgba(37,71,106,1)',
          borderWidth: 2
        }, {
          label: 'Documentos',
          data: documentos,
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