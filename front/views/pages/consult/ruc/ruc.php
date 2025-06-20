<div class="tab-base p-relative">

    <!-- Nav tabs -->
    <ul class="nav nav-callout">
        <li class="nav-item waves-effect" onclick="window.open('/consult/ruc','_self');">
            <button class="nav-link active" type="button">Consulta RUC</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/consult/dni','_self');">
            <button class="nav-link" type="button">Consulta DNI</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/consult/cpe','_self');">
            <button class="nav-link" type="button">Consulta CPE</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/consult/exchange','_self');">
            <button class="nav-link" type="button">Consulta Tipo Cambio</button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content br-bottom">

        <div class="tab-pane fade show active">
            <h5 class="card-title">Consulta RUC</h5>
            <p>Ingresa el número de RUC a consultar.</p>
        </div>

        <div>

            <input type="hidden" value="11" id="doc-long">

            <div class="row">

                <!--=====================================
                RUC a buscar
                ======================================-->
                <div class="col-md-4">

                    <div class="form-group form-floating mt-2 mb-3">

                        <input
                        type="text"
                        class="form-control documento"
                        maxlength="11"
                        pattern="[0-9]{1,}"
                        id="doc-person"
                        name="ruc-tenant"
                        placeholder="RUC"
                        autocomplete="off"
                        required>

                        <label>RUC <span id="estado-ruc"></span> <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <div class="col-md-2">

                    <button type="button" class="btn btn-primary mt-2 mb-3" onclick="sendAjaxConsult('ruc')"><i class="fa fa-search"></i> Consultar</button>

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