<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Requerimos las clases
=============================================*/
require_once "controllers/tenants.controller.php";
$tenants = TenantsController::ctrListTenants();

$allTenants = $dataUsers->id_empresa_usuario;

?>

<?php if (empty($_SESSION["admin"])): ?>

    <div id="root" class="root mn--max hd--expanded mn--sticky">

        <section id="content" class="content">

            <div class="content__boxed">
                <div class="content__wrap">

                    <div class="row">

                        <div class="col-md-3 mb-3">

                            <!-- Basic card -->
                            <div class="card h-100">
                                <div style="background-color: #25476a; height: 125px; border-radius: 10px;"></div>
                                <div class="position-relative p-3">
                                    <div class="position-absolute top-0 start-50 translate-middle text-white">
                                        <img class="img-lg rounded-circle border border-white border-3" src="<?php echo $avatarUser ?>">
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <a class="h5 btn-link pointer"><?php echo $dataUsers->nombres_usuario; ?></a>
                                    <p class="text-opacity-75 mb-0"><?php echo $dataUsers->email_usuario; ?></p>
                                </div>

                                <div class="card-body">
                                    <div class="list-group list-group-borderless">
                                        <a class="list-group-item list-group-item-action pointer"><i class="fa fa-phone fs-4 me-2"></i> <b>Tel&eacute;fono: </b><?php echo $dataUsers->telefono_usuario; ?></a>
                                        <a class="list-group-item list-group-item-action pointer"><i class="fa fa-calendar-o fs-4 me-2"></i> <b>Registrado: </b><?php echo TemplateController::fechaEsShort($dataUsers->creado_usuario); ?></a>
                                        <a class="list-group-item list-group-item-action pointer"><i class="fa fa-user-o fs-4 me-2"></i> <b>Alias: </b><?php echo $dataUsers->alias_usuario; ?></a>
                                    </div>
                                </div>

                                <div class="d-grid p-2">
                                    <a href="/logout" class="btn btn-danger">Cerrar Sesión</a>
                                </div>
                            </div>
                            <!-- END : Basic card -->

                        </div>
                        <div class="col-md-9 mb-3">

                            <!-- Card with header -->
                            <div class="card h-100">
                                <div class="card-header">
                                    <span style="font-size: 16px"><?php echo $dataSett->nombre_sistema_configuracion ?></span>
                                    <?php if($_SESSION["user"]->rol_usuario != 3): ?>
                                    <div class="pull-right">
                                        <a href="/cart" class="btn btn-primary hstack gap-2 align-self-center">
                                            <i class="demo-psi-add fs-5"></i>
                                            <span class="vr"></span>
                                            Agregar una Empresa
                                        </a>
                                    </div>
                                    <?php endif ?>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered table-sm">

                                            <?php if($tenants > 0): ?>

                                                <?php if($allTenants != ''): ?>

                                                <thead class="bg-default">
                                                    <tr>
                                                        <th class="text-center hidden-xs">#</th>
                                                        <th class="text-center hidden-xs">Logo</th>
                                                        <th>Empresa</th>
                                                    </tr>
                                                </thead>

                                                <?php endif ?>
                                            
                                            <?php endif ?>

                                            <tbody>
                                                <?php

                                                    $url = "/";
                                                    $a   = $url;                                                
                                                    
                                                    if($tenants > 0) {

                                                        if($allTenants != '') {

                                                            foreach($tenants as $mydata) {

                                                                $jsonTenant = json_decode($allTenants, true);
            
                                                                foreach ($jsonTenant as $key => $value) {
                
                                                                    if($value["id"] == $mydata->id_empresa) {

                                                                        /*=============================================
                                                                        Redirigimos con la sesion de la empresa
                                                                        =============================================*/
                                                                        for($i = 1 ;$i<=$mydata->id_empresa;$i++) {

                                                                            $urlEncode = base64_encode($i . '~' . $_SESSION['user']->token_usuario);

                                                                            $tienda_actual = "window.open('redirect/".$urlEncode."','_self')";

                                                                        }

                                                                        /*=============================================
                                                                        Validamos la fase
                                                                        =============================================*/
                                                                        if($mydata->fase_empresa == 'beta') {

                                                                            $bdFase = 'warning';

                                                                        } else {

                                                                            $bdFase = 'success';

                                                                        }

                                                                        if($mydata->logo_empresa == '') {

                                                                            $imgTenant = TemplateController::returnImgDefault('logo.png', '');

                                                                        } else {

                                                                            $imgTenant = TemplateController::returnImg('logo/'.$mydata->ruc_empresa, $mydata->logo_empresa);

                                                                        }
                                                                        
                                                                        echo '<tr onclick="'.$tienda_actual.'" class="pointer">
                                                                                <td class="text-center hidden-xs"><span class="badge badge-info mt-3">'.($key+1).'</span></td>
                                                                                <td class="text-center hidden-xs">
                                                                                    <img class="border border-default border-2 mt-2" src="'.$imgTenant.'" height="40">
                                                                                </td>
                                                                                <td>
                                                                                    <span class="h6">'.$mydata->razon_social_empresa.'</span> <sup class="badge badge-'.$bdFase.'">'.$mydata->fase_empresa.'</sup>
                                                                                    <br>
                                                                                    <small class="text-muted">'.$mydata->ruc_empresa.'</small>
                                                                                    <br>
                                                                                    <small class="text-muted">'.$mydata->direccion_empresa.'</small>
                                                                                </td>
                                                                            </tr>';

                                                                    }
                
                                                                }

                                                            }

                                                        } else {

                                                            echo '<div class="alert alert-danger">No tienes empresas registradas.</div>';

                                                        }

                                                    } else {

                                                        echo '<div class="alert alert-danger">Aún no hay empresas registradas.</div>';

                                                    }

                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php if($_SESSION["user"]->rol_usuario == 1): ?>

                                    <?php
                                        
                                        /*=============================================
                                        Creamos las variables para el panel admin
                                        =============================================*/
                                        $urlEncode = base64_encode('0~' . $_SESSION['user']->token_usuario);
                                        $tienda_actual = "window.open('redirect/".$urlEncode."','_self')";
                                        
                                    ?>

                                    <div class="card-footer text-center">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <a class="btn btn-info pointer" onclick="<?php echo $tienda_actual ?>" style="width:100%;">Panel Admin</a>
                                            </div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    <div>

                                <?php endif ?>
                            </div>
                            <!-- END : Card with header -->

                        </div>

                    </div>

                </div>
            </div>

        </section>

    </div>

<?php elseif (!empty($_SESSION["admin"])): ?>

    <div class="content__header content__boxed overlapping">
        <div class="content__wrap">

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">

                    <li class="breadcrumb-item"><a href="/">Inicio</a></li>

                    <?php if (isset($routesArray[2])): ?>

                        <?php if ($routesArray[2] == "new" || $routesArray[2] == "edit"): ?>

                            <li class="breadcrumb-item"><a href="/tenants">Empresas</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo $txtBread ?></li>

                        <?php endif?>

                    <?php else: ?>

                        <li class="breadcrumb-item active" aria-current="page">Empresas</li>

                    <?php endif?>

                </ol>
            </nav>
            <!-- END : Breadcrumb -->

            <h1 class="page-title mb-0 mt-2">Empresas</h1>
            <p class="lead">En esta sección podrás administrar e ingresar al panel de cada empresa registrada.</p>

        </div>

    </div>

    <div class="content__boxed">
        <div class="content__wrap">

        <?php

            if (isset($routesArray[2])) {

                if ($routesArray[2] == "new" || $routesArray[2] == "edit" || $routesArray[2] == "view") {

                    include "actions/" . $routesArray[2] . ".php";

                }

            } else {

                include "actions/list.php";

            }

        ?>

        </div>

    </div>

<?php endif ?>