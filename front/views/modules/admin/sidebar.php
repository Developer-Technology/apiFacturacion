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
                            <!--<a href="#" class="nav-link waves-effect">
                                <img src="<?php echo TemplateController::returnImgDefault('lock1.svg', 'svg') ?>" style="width: 15px; margin-top: -2.5px;">
                                <i class="me-2"></i>
                                <span class="ms-1">Bloquear Sesión</span>
                            </a>-->
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

                    <?php if($dataUsers->rol_usuario == 1): ?>

                    <li class="nav-item hidden">
                        <a href="/plans" class="nav-link mininav-toggle waves-effect <?php if (!empty($routesArray) && $routesArray[1] == 'plans'): ?>active<?php endif?>">
                            <img src="<?php echo TemplateController::returnImgDefault('member.svg', 'svg') ?>" style="width: 15px; margin-top: -2.5px;">
                            <i class="me-2"></i>
                            <span class="nav-label mininav-content ms-1">Planes</span>
                        </a>
                    </li>

                    <?php endif ?>
                    
                </ul>
            </div>

            <!-- Components Category -->
            <div class="mainnav__categoriy py-2">
                <h6 class="mainnav__caption mt-0 px-3 fw-bold">Menú De Navegación</h6>
                <ul class="mainnav__menu nav flex-column">

                    <li class="nav-item">
                        <a href="/plans" class="nav-link mininav-toggle waves-effect <?php if (!empty($routesArray) && $routesArray[1] == 'plans'): ?>active<?php endif?>">
                            <img src="<?php echo TemplateController::returnImgDefault('group.svg', 'svg') ?>" style="width: 15px; margin-top: -2.5px;">
                            <i class="me-2"></i>
                            <span class="nav-label mininav-content ms-1">Planes</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/tenants" class="nav-link mininav-toggle waves-effect <?php if (!empty($routesArray) && $routesArray[1] == 'tenants'): ?>active<?php endif?>">
                            <img src="<?php echo TemplateController::returnImgDefault('businesess.png', '') ?>" style="width: 15px; margin-top: -2.5px;">
                            <i class="me-2"></i>
                            <span class="nav-label mininav-content ms-1">Empresas</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/sales" class="nav-link mininav-toggle waves-effect <?php if (!empty($routesArray) && $routesArray[1] == 'sales'): ?>active<?php endif?>">
                            <img src="<?php echo TemplateController::returnImgDefault('shopping-bag1.svg', 'svg') ?>" style="width: 15px; margin-top: -2.5px;">
                            <i class="me-2"></i>
                            <span class="nav-label mininav-content ms-1">Ventas</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/users" class="nav-link mininav-toggle waves-effect <?php if (!empty($routesArray) && $routesArray[1] == 'users'): ?>active<?php endif?>">
                            <img src="<?php echo TemplateController::returnImgDefault('meeting.svg', 'svg') ?>" style="width: 15px; margin-top: -2.5px;">
                            <i class="me-2"></i>
                            <span class="nav-label mininav-content ms-1">Usuarios</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/settings/general" class="nav-link mininav-toggle waves-effect <?php if (!empty($routesArray) && $routesArray[1] == 'settings'): ?>active<?php endif?>">
                            <img src="<?php echo TemplateController::returnImgDefault('settings1.svg', 'svg') ?>" style="width: 15px; margin-top: -2.5px;">
                            <i class="me-2"></i>
                            <span class="nav-label mininav-content ms-1">Configuraciones</span>
                        </a>
                    </li>

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
                    <h6 class="mainnav__caption px-3 fw-bold">Espacio En Disco Duro</h6>
                    <ul class="list-group list-group-borderless">
                        <li class="list-group-item text-reset">
                            <div class="d-flex justify-content-between align-items-start">
                                <p class="mb-2 me-auto">Espacio Disponible</p>
                                <span class="badge bg-info rounded"><?php print("$freeDisk GB");?></span>
                            </div>
                            <div class="progress progress-md">
                                <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $perncentDiskFree ?>%"
                                    aria-label="CPU Progress" aria-valuenow="<?php echo $perncentDiskFree ?>" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <small class="text-muted"><?php print("De $totalDisk GB");?></small>
                        </li>
                        <li class="list-group-item text-reset">
                            <div class="d-flex justify-content-between align-items-start">
                                <p class="mb-2 me-auto">Espacio Utilizado</p>
                                <span class="badge bg-warning rounded"><?php print("$utilizadoDisk GB");?></span>
                            </div>
                            <div class="progress progress-md">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $perncentDiskUtil ?>%"
                                    aria-label="Bandwidth Progress" aria-valuenow="<?php echo $perncentDiskUtil ?>" aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>
                        </li>
                    </ul>
                    <div class="d-grid px-3 mt-3">
                        <a href="/suscriptions" class="btn btn-sm btn-success">Suscripciones</a>
                    </div>
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