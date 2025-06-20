<header class="header">
    <div class="header__inner">

        <!-- Brand -->
        <div class="header__brand">
            <div class="brand-wrap waves-effect">

                <!-- Brand logo -->
                <a href="/" class="brand-img stretched-link">
                    <img src="<?php echo $faviSet; ?>" alt="<?php echo $dataSett->nombre_sistema_configuracion; ?>" class="<?php echo $dataTenants->ruc_empresa; ?>" width="40" height="40">
                </a>

                <!-- Brand title -->
                <small><?php echo $dataTenants->razon_social_empresa; ?></small>

            </div>
        </div>
        <!-- End - Brand -->

        <div class="header__content">

            <!-- Content Header - Left Side: -->
            <div class="header__content-start">

                <!-- Navigation Toggler -->
                <button type="button" class="nav-toggler header__btn btn btn-icon btn-sm" aria-label="Nav Toggler">
                    <i class="demo-psi-view-list"></i>
                </button>

            </div>
            <!-- End - Content Header - Left Side -->

            <!-- Content Header - Right Side: -->
            <div class="header__content-end">

                <!-- Notification Dropdown -->
                <div class="dropdown">

                    <!-- Toggler -->
                    <button class="header__btn btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-label="Notification dropdown" aria-expanded="false">
                        <span class="d-block position-relative">
                            <i class="demo-psi-bell"></i>
                            <span class="badge badge-super rounded bg-danger p-1">
                                <span class="">0</span>
                            </span>
                        </span>
                    </button>

                    <!-- Notification dropdown menu -->
                    <div class="dropdown-menu dropdown-menu-end w-md-300px">
                        <div class="border-bottom px-3 py-2 mb-3">
                            <h5>Notificaciones</h5>
                        </div>

                        <div class="list-group list-group-borderless">

                            <?php if (
                                strtotime($fechaHoy) >=
                                strtotime($fechaValidacion)
                            ): ?>
                            <!-- List item -->
                            <div class="list-group-item list-group-item-action d-flex align-items-start mb-3 waves-effect" onclick="window.open('/suscriptions','_self');">
                                <div class="flex-shrink-0 me-3">
                                    <i class="fa fa-credit-card text-secondary-300 fs-2"></i>
                                </div>
                                <div class="flex-grow-1 ">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <a class="pointer h6 mb-0 stretched-link text-decoration-none">Facturación</a>
                                        <span class="badge bg-secondary rounded ms-auto"><?php echo $prxFact; ?></span>
                                    </div>
                                    <small class="text-muted">Fecha De Su Próxima Facturación</small>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- List item -->
                            <div class="list-group-item list-group-item-action d-flex align-items-start mb-3 waves-effect">
                                <div class="flex-shrink-0 me-3">
                                    <i class="fa fa-search text-blue-200 fs-2"></i>
                                </div>
                                <div class="flex-grow-1 ">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <a class="pointer h6 mb-0 stretched-link text-decoration-none">Consultas</a>
                                        <span class="badge bg-info rounded ms-auto"><?php echo $dispCons; ?></span>
                                    </div>
                                    <small class="text-muted">Consultas Disponibles</small>
                                </div>
                            </div>

                            <div class="list-group-item list-group-item-action d-flex align-items-start mb-3 waves-effect">
                                <div class="flex-shrink-0 me-3">
                                    <i class="fa fa-file text-green-200 fs-2"></i>
                                </div>
                                <div class="flex-grow-1 ">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <a class="pointer h6 mb-0 stretched-link text-decoration-none">Documentos</a>
                                        <span class="badge bg-info rounded ms-auto"><?php echo $dispDocs; ?></span>
                                    </div>
                                    <small class="text-muted">Documentos Disponibles</small>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- End - Notification dropdown -->

                <!-- User dropdown -->
                <div class="dropdown">

                    <!-- Toggler -->
                    <button class="header__btn btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-label="User dropdown" aria-expanded="false">
                        <i class="demo-psi-male"></i>
                    </button>

                    <!-- User dropdown menu -->
                    <div class="dropdown-menu dropdown-menu-end w-md-450px">

                        <!-- User dropdown header -->
                        <div class="d-flex align-items-center border-bottom px-3 py-2">
                            <div class="flex-shrink-0">
                                <img class="img-sm rounded-circle border" src="<?php echo $avatarUser; ?>" oading="lazy">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0">
                                    <?php echo $dataUsers->nombres_usuario; ?>
                                </h5>
                                <span class="text-muted fst-italic">
                                    <a class="__cf_email__ pointer"><?php echo $dataUsers->email_usuario; ?></a>
                                </span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-7">

                                <!-- Simple widget and reports -->
                                <div class="list-group list-group-borderless mb-3">
                                    <div class="list-group-item text-center border-bottom mb-3">
                                        <p class="h1 display-1 text-green"><?php echo $realCons; ?></p>
                                        <p class="h6 mb-0">
                                            <img src="<?php echo TemplateController::returnImgDefault(
                                                "search.png",
                                                ""
                                            ); ?>" style="width: 15px; margin-top: -2.5px;">
                                            <i class="fs-3 me-2"></i>
                                            Consultas Realizadas
                                        </p>
                                    </div>
                                    <div class="list-group-item py-0 d-flex justify-content-between align-items-center">
                                        Consultado
                                        <small class="fw-bolder"><?php echo $realCons; ?></small>
                                    </div>
                                    <div class="list-group-item py-0 d-flex justify-content-between align-items-center">
                                        Disponible
                                        <small class="fw-bolder text-danger"><?php echo $dispCons; ?></small>
                                    </div>
                                    <div class="list-group-item py-0 d-flex justify-content-between align-items-center">
                                        Total
                                        <span class="fw-bold text-primary"><?php echo $totalCons; ?></span>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-5">

                                <!-- User menu link -->
                                <div class="list-group list-group-borderless h-100 py-3">
                                    <a href="/profile" class="list-group-item list-group-item-action waves-effect">
                                        <img src="<?php echo TemplateController::returnImgDefault(
                                            "profile.png",
                                            ""
                                        ); ?>" style="width: 15px; margin-top: -2.5px;">
                                        <i class="fs-5 me-2"></i>
                                        Mi Perfil
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action waves-effect hidden">
                                        <img src="<?php echo TemplateController::returnImgDefault(
                                            "document.png",
                                            ""
                                        ); ?>" style="width: 15px; margin-top: -2.5px;">
                                        <i class="fs-5 me-2"></i>
                                        Documentación
                                    </a>
                                    <a href="/change" class="list-group-item list-group-item-action waves-effect">
                                        <img src="<?php echo TemplateController::returnImgDefault(
                                            "change.png",
                                            ""
                                        ); ?>" style="width: 15px; margin-top: -2.5px;">
                                        <i class="fs-5 me-2"></i>
                                        Cambiar Empresa
                                    </a>
                                    <a href="/logout" class="list-group-item list-group-item-action waves-effect">
                                        <img src="<?php echo TemplateController::returnImgDefault(
                                            "arrow.svg",
                                            "svg"
                                        ); ?>" style="width: 15px; margin-top: -2.5px;">
                                        <i class="fs-5 me-2"></i>
                                        Cerrar Sesión
                                    </a>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <!-- End - User dropdown -->

            </div>
        </div>
    </div>
</header>
