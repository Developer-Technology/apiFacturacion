<div class="tab-base p-relative">

    <!-- Nav tabs -->
    <ul class="nav nav-callout">
        <li class="nav-item waves-effect" onclick="window.open('/settings/general','_self');">
            <button class="nav-link" type="button">General</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/settings/server','_self');">
            <button class="nav-link active" type="button">Servidor Correo</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/settings/logo','_self');">
            <button class="nav-link" type="button">Logo</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/settings/favicon','_self');">
            <button class="nav-link" type="button">Favicon</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/settings/billing','_self');">
            <button class="nav-link" type="button">Facturación</button>
        </li>
        <li class="nav-item waves-effect" onclick="window.open('/settings/gateway','_self');">
            <button class="nav-link" type="button">Pasarelas De Pago</button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content br-bottom">

        <div class="tab-pane fade show active">
            <h5 class="card-title">Servidor De Correo</h5>
            <p>Requerido para la configuración de correos enviados</p>
        </div>

        <form method="post" class="needs-validation" novalidate autocomplete="off">

            <div class="row">

                <!--=====================================
                Host
                ======================================-->
                <div class="col-md-4">

                    <div class="form-floating mt-2 mb-3">

                        <input type="text" name="host-server" class="form-control" placeholder="Host" value="<?php echo $dataSett->servidor_correo_configuracion ?>" required>
                        <label for="">Host <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Usuario
                ======================================-->
                <div class="col-md-4">

                    <div class="form-floating mt-2 mb-3">

                        <input type="text" name="user-server" class="form-control" placeholder="Usuario" value="<?php echo $dataSett->usuario_correo_configuracion ?>" required>
                        <label for="">Usuario <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Clave
                ======================================-->
                <div class="col-md-4">

                    <div class="form-floating mt-2 mb-3">

                        <input type="text" name="pass-server" class="form-control" placeholder="Contraseña" value="<?php echo $dataSett->clave_correo_configuracion ?>" required>
                        <label for="">Contraseña <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Puerto
                ======================================-->
                <div class="col-md-4">

                    <div class="form-floating mt-2 mb-3">

                        <input type="text" name="port-server" class="form-control" placeholder="Puerto" value="<?php echo $dataSett->puerto_correo_configuracion ?>" required>
                        <label for="">Puerto <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Seguridad
                ======================================-->
                <div class="col-md-4">

                    <div class="form-floating mt-2 mb-3">

                        <input type="text" name="sec-server" class="form-control" placeholder="Seguridad" value="<?php echo $dataSett->seguridad_correo_configuracion ?>" required>
                        <label for="">Seguridad <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <!--=====================================
                Activar / Desactivar
                ======================================-->
                <div class="col-md-4">

                    <div class="form-floating mt-2 mb-3">

                        <select name="act-server" class="form-select" placeholder="Activo?" required>
                            <?php if($dataSett->activo_correo_configuracion == "si"): ?>
                                <option value="si" selected>Sí</option>
                                <option value="no">No</option>
                            <?php else: ?>
                                <option value="si">Sí</option>
                                <option value="no" selected>No</option>
                            <?php endif ?>
                        </select>
                        <label for="">Activo? <sup class="text-danger">*</sup></label>

                    </div>

                </div>

                <?php

                    /*=============================================
                    Controladores
                    =============================================*/
                    require_once "controllers/settings.controller.php";

                    $edit = new SettingsController();
                    $edit->editServer();

                ?>

                <!--=====================================
                Botones
                ======================================-->
                <div class="col-md-12 mt-4 text-center">

                    <a class="btn btn-danger" href="/settings/server">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar</button>

                </div>

            </div>

        </form>

    </div>

</div>