<nav id="mainnav-container" class="mainnav">

    <div class="mainnav__inner">

        <!-- Navigation menu -->
        <div class="mainnav__top-content scrollable-content pb-5">

            <!-- Profile Widget -->
            <div class="mainnav__profile mt-3 d-flex3">

                <div class="mt-2 d-mn-max"></div>

                <!-- Profile picture  -->
                <div class="mininav-toggle text-center py-2">
                    <img class="mainnav__avatar img-md rounded-circle border" src="<?php echo $avatarUser ?>">
                </div>

                <div class="mininav-content collapse d-mn-max">
                    <div class="d-grid">

                        <!-- User name and position -->
                        <button class="d-block btn shadow-none p-2 waves-effect" data-bs-toggle="collapse" data-bs-target="#usernav"
                            aria-expanded="false" aria-controls="usernav">
                            <span class="dropdown-toggle d-flex justify-content-center align-items-center">
                                <h6 class="mb-0 me-3">
                                    <?php echo $dataUsers->alias_usuario; ?>
                                </h6>
                            </span>
                            <small class="text-muted"><?php echo $dataUsers->email_usuario; ?></small>
                        </button>

                        <!-- Collapsed user menu -->
                        <div id="usernav" class="nav flex-column collapse">
                            <a href="/profile" class="nav-link waves-effect <?php if (!empty($routesArray) && $routesArray[1] == 'profile'): ?>active<?php endif?>">
                                <img src="<?php echo TemplateController::returnImgDefault('profile.png', '') ?>" style="width: 15px; margin-top: -2.5px;">
                                <i class="me-2"></i>
                                <span class="ms-1">Mi Perfil</span>
                            </a>
                            <a href="/logout" class="nav-link waves-effect">
                                <img src="<?php echo TemplateController::returnImgDefault('arrow.svg', 'svg') ?>" style="width: 15px; margin-top: -2.5px;">
                                <i class="me-2"></i>
                                <span class="ms-1">Cerrar Sesión</span>
                            </a>
                        </div>

                    </div>
                </div>

            </div>
            <!-- End - Profile widget -->

            <div class="mainnav__categoriy py-3">
                <ul class="mainnav__menu nav flex-column">
                    <li class="nav-item">
                        <a href="/" class="nav-link mininav-toggle waves-effect <?php if (empty($routesArray)): ?>active<?php endif?>">
                            <img src="<?php echo TemplateController::returnImgDefault('house.svg', 'svg') ?>" style="width: 15px; margin-top: -2.5px;">
                            <i class="me-2"></i>
                            <span class="nav-label mininav-content ms-1">Dashboard</span>
                        </a>
                    </li>
                    
                </ul>
            </div>

            <!-- Components Category -->
            <div class="mainnav__categoriy py-2">
                <h6 class="mainnav__caption mt-0 px-3 fw-bold">Menú De Navegación</h6>
                <ul class="mainnav__menu nav flex-column">

                    <?php if($_SESSION["user"]->rol_usuario != 3): ?>
                    <li class="nav-item">
                        <a href="/businesses/general" class="nav-link mininav-toggle waves-effect <?php if (!empty($routesArray) && $routesArray[1] == 'businesses'): ?>active<?php endif?>">
                            <img src="<?php echo TemplateController::returnImgDefault('businesess.png', '') ?>" style="width: 15px; margin-top: -2.5px;">
                            <i class="me-2"></i>
                            <span class="nav-label mininav-content ms-1">Empresa</span>
                        </a>
                    </li>
                    <?php endif ?>

                    <?php if($_SESSION["user"]->rol_usuario != 3): ?>
                    <!-- Link with submenu -->
                    <li class="nav-item has-sub">

                        <a href="#" class="mininav-toggle nav-link waves-effect collapsed <?php if (!empty($routesArray) && $routesArray[1] == 'consult'): ?>active<?php endif?>">
                            <img src="<?php echo TemplateController::returnImgDefault('search.png', '') ?>" style="width: 15px; margin-top: -2.5px;">
                            <i class="me-2"></i>
                            <span class="nav-label ms-1">Consultas</span>
                        </a>

                        <!-- Ui Elements submenu list -->
                        <ul class="mininav-content nav collapse">
                            <li class="nav-item">
                                <a href="/consult/ruc" class="nav-link waves-effect <?php if (!empty($routesArray) && $routesArray[2] == 'ruc'): ?>active<?php endif?>">Consulta RUC</a>
                            </li>
                            <li class="nav-item">
                                <a href="/consult/dni" class="nav-link waves-effect <?php if (!empty($routesArray) && $routesArray[2] == 'dni'): ?>active<?php endif?>">Consulta DNI</a>
                            </li>
                            <li class="nav-item">
                                <a href="/consult/cpe" class="nav-link waves-effect <?php if (!empty($routesArray) && $routesArray[2] == 'cpe'): ?>active<?php endif?>">Consulta CPE</a>
                            </li>
                            <li class="nav-item">
                                <a href="/consult/exchange" class="nav-link waves-effect <?php if (!empty($routesArray) && $routesArray[2] == 'exchange'): ?>active<?php endif?>">Consulta Tipo Cambio</a>
                            </li>
                        </ul>
                        <!-- END : Ui Elements submenu list -->

                    </li>
                    <!-- END : Link with submenu -->
                    <?php endif ?>

                    <!-- Link with submenu -->
                    <li class="nav-item has-sub">

                        <a href="#" class="mininav-toggle nav-link waves-effect collapsed <?php if (!empty($routesArray) && $routesArray[1] == 'despatch-remitent' || $routesArray[1] == 'despatch-transport' || $routesArray[1] == 'summary' || $routesArray[1] == 'voided' || $routesArray[1] == 'documents'): ?>active<?php endif?>">
                            <img src="<?php echo TemplateController::returnImgDefault('sunat.svg', 'svg') ?>" style="width: 15px; margin-top: -2.5px;">
                            <i class="me-2"></i>
                            <span class="nav-label ms-1">Doc. Electrónicos</span>
                        </a>

                        <!-- Ui Elements submenu list -->
                        <ul class="mininav-content nav collapse">
                            <?php if($_SESSION["user"]->rol_usuario != 3): ?>
                            <li class="nav-item has-sub">
                                <a href="#" class="mininav-toggle nav-link collapsed <?php if (!empty($routesArray) && $routesArray[1] == "despatch-remitent" || $routesArray[1] == "despatch-transport"): ?>active<?php endif ?>">Guía Remisión</a>
                                <ul class="mininav-content nav collapse">
                                    <li class="nav-item">
                                        <a href="/despatch-remitent" class="nav-link <?php if (!empty($routesArray) && $routesArray[1] == "despatch-remitent"): ?>active<?php endif ?>">Remitente</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="/despatch-transport" class="nav-link <?php if (!empty($routesArray) && $routesArray[1] == "despatch-transport"): ?>active<?php endif ?>">Transportista</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="/summary" class="nav-link waves-effect <?php if (!empty($routesArray) && $routesArray[1] == 'summary'): ?>active<?php endif?>">Resumen Boletas</a>
                            </li>
                            <li class="nav-item">
                                <a href="/voided" class="nav-link waves-effect <?php if (!empty($routesArray) && $routesArray[1] == 'voided'): ?>active<?php endif?>">Comunicación Baja</a>
                            </li>
                            <?php endif ?>
                            <li class="nav-item">
                                <a href="/documents" class="nav-link waves-effect <?php if (!empty($routesArray) && $routesArray[1] == 'documents'): ?>active<?php endif?>">Historial</a>
                            </li>
                        </ul>
                        <!-- END : Ui Elements submenu list -->

                    </li>
                    <!-- END : Link with submenu -->

                </ul>
            </div>
            <!-- END : Navigation Category -->

            <!-- Widget -->
            <div class="mainnav__profile">

                <!-- Widget buttton form small navigation -->
                <div class="mininav-toggle text-center py-2 d-mn-min">
                    <i class="demo-pli-monitor-2"></i>
                </div>

                <div class="d-mn-max mt-3"></div>

                <!-- Widget content -->
                <div class="mininav-content collapse d-mn-max">
                    <h6 class="mainnav__caption px-3 fw-bold">Consumo Del Plan</h6>
                    <ul class="list-group list-group-borderless">
                        <li class="list-group-item text-reset">
                            <div class="d-flex justify-content-between align-items-start">
                                <p class="mb-2 me-auto">Consultas Disponibles</p>
                                <span class="badge bg-info rounded"><?php echo $dispCons ?></span>
                            </div>
                            <div class="progress progress-md">
                                <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $dispCons ?>%" aria-label="CPU Progress" aria-valuenow="<?php echo $dispCons ?>" aria-valuemin="<?php echo $dispCons ?>" aria-valuemax="<?php echo $totalCons ?>">
                                </div>
                            </div>
                            <small class="text-muted">De <b><?php echo $totalCons ?></b> consultas</small>
                        </li>
                        <li class="list-group-item text-reset">
                            <div class="d-flex justify-content-between align-items-start">
                                <p class="mb-2 me-auto">Consultas Realizadas</p>
                                <span class="badge bg-warning rounded"><?php echo $realCons ?></span>
                            </div>
                            <div class="progress progress-md">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $realCons ?>%" aria-label="Bandwidth Progress" aria-valuenow="<?php echo $realCons ?>" aria-valuemin="<?php echo $realCons ?>" aria-valuemax="<?php echo $totalCons ?>"></div>
                            </div>
                        </li>
                    </ul>
                    <?php if($_SESSION["user"]->rol_usuario != 3): ?>
                    <div class="d-grid px-3 mt-3">
                        <a href="/suscriptions" class="btn btn-sm btn-success">Mi Suscripción</a>
                    </div>
                    <?php endif ?>
                </div>
            </div>
            <!-- End - Profile widget -->

        </div>
        <!-- End - Navigation menu -->

        <!-- Bottom navigation menu -->
        <div class="mainnav__bottom-content border-top pb-2">
            <ul id="mainnav" class="mainnav__menu nav flex-column">
                <li class="nav-item has-sub">
                    <a href="/logout" class="nav-link mininav-toggle collapsed" aria-expanded="false">
                        <img src="<?php echo TemplateController::returnImgDefault('arrow.svg', 'svg') ?>" style="width: 15px; margin-top: -2.5px;">
                        <i class="me-2"></i>
                        <span class="nav-label ms-1">Cerrar Sesión</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- End - Bottom navigation menu -->

    </div>
</nav>