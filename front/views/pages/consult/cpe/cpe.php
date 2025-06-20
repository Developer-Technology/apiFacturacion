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
            <button class="nav-link active" type="button">Consulta CPE</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/consult/exchange','_self');">
            <button class="nav-link" type="button">Consulta Tipo Cambio</button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content br-bottom">

        <div class="tab-pane fade show active">
            <h5 class="card-title">Consulta CPE</h5>
            <p>Ingresa los datos del CPE a consultar.</p>
        </div>

        <div>

            <div class="row">

                <!--=====================================
                Tipo de comprobante
                ======================================-->
                <div class="col-md-4">

                    <div class="form-group form-floating mt-2 mb-3">

                        <select name="type-comp" id="type-comp" class="form-select">
                            <option value="">Selecciona una opción</option>
                            <option value="01">Factura</option>
                            <option value="03">Boleta</option>
                            <option value="07">Nota de Crédito</option>
                            <option value="08">Nota de Débito</option>
                        </select>

                        <label>Tipo <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Ruc del emisor
                ======================================-->
                <div class="col-md-4">

                    <div class="form-group form-floating mt-2 mb-3">

                        <input
                        type="text"
                        class="form-control"
                        maxlength="11"
                        id="ruc-emisor"
                        name="ruc-emisor"
                        placeholder="RUC del Emisor"
                        autocomplete="off"
                        required>

                        <label>RUC del Emisor <sup class="text-danger">*</sup></label>

                    </div>

                </div>
                
                <!--=====================================
                Serie del comprobante
                ======================================-->
                <div class="col-md-4">

                    <div class="form-group form-floating mt-2 mb-3">

                        <input
                        type="text"
                        class="form-control"
                        maxlength="4"
                        id="serie-comp"
                        name="serie-comp"
                        placeholder="Serie"
                        autocomplete="off"
                        required>

                        <label>Serie <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Correlativo del comprobante
                ======================================-->
                <div class="col-md-4">

                    <div class="form-group form-floating mt-2 mb-3">

                        <input
                        type="text"
                        class="form-control"
                        maxlength="8"
                        pattern="[0-9]{1,}"
                        id="number-comp"
                        name="number-comp"
                        placeholder="Número"
                        autocomplete="off"
                        required>

                        <label>Número <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Fecha de emision
                ======================================-->
                <div class="col-md-4">

                    <div class="form-group form-floating mt-2 mb-3">

                        <input
                        type="date"
                        class="form-control"
                        id="emite-comp"
                        name="emite-comp"
                        placeholder="Fecha de Emisión"
                        autocomplete="off"
                        required>

                        <label>Fecha de Emisión <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Fecha de emision
                ======================================-->
                <div class="col-md-2">

                    <div class="form-group form-floating mt-2 mb-3">

                        <input
                        type="number"
                        class="form-control"
                        id="monto-comp"
                        name="monto-comp"
                        placeholder="Monto"
                        autocomplete="off"
                        required>

                        <label>Monto <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <div class="col-md-2">

                    <button type="button" class="btn btn-primary mt-2 mb-3" style="width: 100%" onclick="sendAjaxConsult('cpe')"><i class="fa fa-search"></i> Consultar</button>

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