<div class="tab-base p-relative">

    <!-- Nav tabs -->
    <ul class="nav nav-callout">
        <li class="nav-item waves-effect" onclick="window.open('/consult/ruc','_self');">
            <button class="nav-link" type="button">Consulta RUC</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/consult/dni','_self');">
            <button class="nav-link" type="button">Consulta DNI</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/consult/cpe','_self');">
            <button class="nav-link" type="button">Consulta CPE</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/consult/exchange','_self');">
            <button class="nav-link active" type="button">Consulta Tipo Cambio</button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content br-bottom">

        <div class="tab-pane fade show active">
            <h5 class="card-title">Consulta Tipo De Cambio</h5>
            <p>La consulta del tipo de cambio se extrae directamente de SUNAT por la fecha actual <b>(<?php echo date('Y-m-d') ?>)</b>.</p>
        </div>

        <div>

            <input type="hidden" value="8" id="doc-long">

            <div class="row">

                <div class="col-md-2">

                    <button type="button" class="btn btn-primary mt-2 mb-3" onclick="sendAjaxConsult('tc')"><i class="fa fa-search"></i> Consultar</button>

                </div>

                <!--=====================================
                Respuesta Json
                ======================================-->
                <div class="col-md-12">

                    <div id="preData" class="code"></div>

                </div>

            </div>

        </div>

    </div>

</div>