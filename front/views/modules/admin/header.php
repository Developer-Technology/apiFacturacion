<header class="header">
    <div class="header__inner">

        <!-- Brand -->
        <div class="header__brand">
            <div class="brand-wrap waves-effect">

                <!-- Brand logo -->
                <a href="/" class="brand-img stretched-link">
                    <img src="<?php echo $faviSet ?>" alt="<?php echo $dataSett->nombre_sistema_configuracion ?>" class="Nifty logo border" width="40" height="40">
                </a>

                <!-- Brand title -->
                <div class="brand-title"><?php echo $dataSett->nombre_sistema_configuracion ?></div>

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
                                <img class="img-sm rounded-circle border" src="<?php echo $avatarUser ?>" oading="lazy">
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
                                        <p class="h1 display-1 text-green"><?php echo $perncentDiskFree; ?><sup><small>%</small></sup></p>
                                        <p class="h6 mb-0">
                                            Disponible de <?php print("$totalDisk GB"); ?>
                                        </p>
                                        <small class="text-muted">Espacio En Disco Duro</small>
                                    </div>
                                    <div class="list-group-item py-0 d-flex justify-content-between align-items-center">
                                        Disponible
                                        <small class="fw-bolder"><?php print("$freeDisk GB"); ?></small>
                                    </div>
                                    <div class="list-group-item py-0 d-flex justify-content-between align-items-center">
                                        Utilizado
                                        <small class="fw-bolder text-danger"><?php print("$utilizadoDisk GB"); ?></small>
                                    </div>
                                    <div class="list-group-item py-0 d-flex justify-content-between align-items-center">
                                        Total
                                        <span class="fw-bold text-primary"><?php print("$totalDisk GB"); ?></span>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-5">

                                <!-- User menu link -->
                                <div class="list-group list-group-borderless h-100 py-3">
                                    <a href="/profile" class="list-group-item list-group-item-action waves-effect">
                                        <img src="<?php echo TemplateController::returnImgDefault('profile.png', '') ?>" style="width: 15px; margin-top: -2.5px;">
                                        <i class="fs-5 me-2"></i>
                                        Mi Perfil
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action waves-effect hidden">
                                        <img src="<?php echo TemplateController::returnImgDefault('document.png', '') ?>" style="width: 15px; margin-top: -2.5px;">
                                        <i class="fs-5 me-2"></i>
                                        Documentación
                                    </a>
                                    <a href="/change" class="list-group-item list-group-item-action waves-effect">
                                        <img src="<?php echo TemplateController::returnImgDefault('change.png', '') ?>" style="width: 15px; margin-top: -2.5px;">
                                        <i class="fs-5 me-2"></i>
                                        Cambiar Entorno
                                    </a>
                                    <a href="/logout" class="list-group-item list-group-item-action waves-effect">
                                        <img src="<?php echo TemplateController::returnImgDefault('arrow.svg', 'svg') ?>" style="width: 15px; margin-top: -2.5px;">
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