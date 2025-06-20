<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Definimos la zona horaria
=============================================*/
date_default_timezone_set('America/Lima');
$fechaHoy = date('Y-m-d');

/*=============================================
Verificamos la sesion de usuario
=============================================*/
session_start();

/*=============================================
Capturar las rutas de la URL
=============================================*/
$routesArray = explode("/", $_SERVER['REQUEST_URI']);
$routesArray = array_filter($routesArray);

/*=============================================
Limpiar la Url de variables GET
=============================================*/
foreach ($routesArray as $key => $value) {

    $value = explode("?", $value)[0];
    $routesArray[$key] = $value;

}

/*=============================================
Obtenemos el estado del servidor
=============================================*/
$freeDisk = round(disk_free_space("/") / 1024 / 1024 / 1024);
$totalDisk = round(disk_total_space("/") / 1024 / 1024 / 1024);
$utilizadoDisk = $totalDisk - $freeDisk;
$perncentDiskFree = round(($freeDisk / $totalDisk) * 100);
$perncentDiskUtil = round(($utilizadoDisk / $totalDisk) * 100);

/*=============================================
Obtenemos las configuraciones iniciales
=============================================*/
require_once "controllers/settings.controller.php";
$dataSett = SettingsController::settings();

/*=============================================
Obtenemos el icono de la empresa o sistema
=============================================*/
if ($dataSett->favicon_sistema_configuracion == '') {

    $faviSet = TemplateController::returnImgDefault('favicon.png', '');

} else {

    $faviSet = TemplateController::returnImg('img/favicon', $dataSett->favicon_sistema_configuracion);

}

/*=============================================
Texto para el breadcrumb
=============================================*/
if (isset($routesArray[2])) {

    if ($routesArray[2] == "new") {

        $txtBread = 'Nuevo';

    } elseif ($routesArray[2] == "edit") {

        $txtBread = 'Editar';

    } elseif ($routesArray[2] == "view") {

        $txtBread = 'Vista';

    } elseif ($routesArray[2] == "pay") {

        $txtBread = 'Pagar';

    } elseif ($routesArray[2] == "upload") {

        $txtBread = 'Cargar';

    } elseif ($routesArray[2] == "verify") {

        $txtBread = 'Verificar';

    } else {

        $txtBread = '';

    }

}

/*=============================================
Obtenemos frases aleatorias
=============================================*/
$vector = array(
    1 => "Nada nuevo hay bajo el sol, pero cuántas cosas viejas hay que no conocemos.",
    2 => "El verdadero amigo es aquel que está a tu lado cuando preferiría estar en otra parte.",
    3 => "La sabiduría es la hija de la experiencia.",
    4 => "Nunca hay viento favorable para el que no sabe hacia dónde va.",
    5 => "No se trata de si van a derribarte, se trata de si vas a levantarte cuando lo hagan.",
    6 => "Nadie puede hacerte sentir inferior sin tu consentimiento.",
    7 => "El pesimista ve dificultades en cada oportunidad. El optimista ve oportunidades en cada dificultad.",
    8 => "El momento en que quieres dejarlo es justo el momento en que tienes que seguir avanzando.",
    9 => "Rodéate de personas que crean en tus sueños, animen tus ideas, apoyen tus ambiciones, y saquen lo mejor de ti.",
    10 => "La mayor parte de las grandes cosas que ha conseguido el hombre, fueron declaradas imposibles antes de que alguien las hiciera.",
);
$numero = rand(1, 10);

/*=============================================
Validamos la sesion del usuario
=============================================*/
if (isset($_SESSION["user"])) {

    /*=============================================
    Obtenemos los datos del usuario logueado
    =============================================*/
    require_once "controllers/users.controller.php";

    $dataUsers = UsersController::dataUser();

    /*=============================================
    Avatar usuario
    =============================================*/
    if ($dataUsers->avatar_usuario == '') {

        $avatarUser = TemplateController::returnImgDefault('default.png', '');

    } else {

        $avatarUser = TemplateController::returnImg('img/users', $dataUsers->avatar_usuario);

    }

}

/*=============================================
Validamos la sesion de la empresa
=============================================*/
if (!empty($_SESSION["empresa"])) {

    /*=============================================
    Obtenemos los datos de la empresa
    =============================================*/
    require_once "controllers/tenants.controller.php";

    $value = $_SESSION["empresa"]->id_empresa;

    $dataTenants = TenantsController::dataTenant($value);

    /*=============================================
    Obtenemos los datos del consumo del periodo actual
    =============================================*/
    if($dataTenants->consumo_empresa != '[]') {

        foreach (json_decode($dataTenants->consumo_empresa) as $key => $cons) {

            $periodo = $cons->periodo;
            $realCons = $cons->consultas;
            $realDocs = $cons->documentos;
    
        }

    } else {

        $realCons = 0;
        $realDocs = 0;

    }

    /*=============================================
    Obtenemos los datos del plan
    =============================================*/
    require_once "controllers/plans.controller.php";
    $valuePlan = $dataTenants->id_plan_empresa;
    $dataPlans = PlansController::dataPlan($valuePlan);

    $jsonPlan = $dataPlans->contiene_plan;

    $arrayPlan = json_decode($jsonPlan, true);

    /*=============================================
    Validamos la cantidad de consultas y documentos
    =============================================*/
    foreach ($arrayPlan as $elementPlan) {

        /*=============================================
        Validamos la cantidad de consultas
        =============================================*/
        if ($elementPlan["consultas"] == "ilimitado") {

            $totalCons = '&#x221e;';
            $dispCons = '&#x221e;';

        } else {

            $totalCons = $elementPlan["consultas"];
            $dispCons = $totalCons - $realCons;

        }

        /*=============================================
        Validamos la cantidad de documentos
        =============================================*/
        if ($elementPlan["documentos"] == "ilimitado") {

            $totalDocs = '&#x221e;';
            $dispDocs = '&#x221e;';

        } else {

            $totalDocs = $elementPlan["documentos"];
            $dispDocs = $totalDocs - $realDocs;

        }

    }

    /*=============================================
    Formateamos la fecha de expiracion del certificado
    =============================================*/
    if ($dataTenants->expira_certificado_empresa == '0000-00-00' || $dataTenants->expira_certificado_empresa == '1969-12-31' || $dataTenants->expira_certificado_empresa == NULL) {

        $expCert = '----';

    } else {

        $expCert = TemplateController::fechaEsShort($dataTenants->expira_certificado_empresa);

    }

    /*=============================================
    Formateamos la fecha de proxima facturacion
    =============================================*/
    if ($dataTenants->proxima_facturacion_empresa == '0000-00-00' || $dataTenants->proxima_facturacion_empresa == '1969-12-31') {

        $prxFact = '----';

        $fechaValidacion = '----';

    } else {

        $prxFact = TemplateController::fechaEsShort($dataTenants->proxima_facturacion_empresa);

        /*=============================================
        Validamos 5 dias antes de la fecha de proxima facturacion
        =============================================*/
        $fechaExpira = $prxFact;
        $fechaValidacion = date('Y-m-d', strtotime('-5 days', strtotime($fechaExpira)));

    }

}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
    <meta name="description" content="Facturacion electronica">

    <?php

$keywords = "";

foreach (json_decode($dataSett->keywords_configuracion, true) as $key => $value) {

    $keywords .= $value . ", ";

}

$keywords = substr($keywords, 0, -2);
$title = $dataSett->nombre_sistema_configuracion . ' | ' . $dataSett->nombre_empresa_configuracion;
$name = $dataSett->nombre_sistema_configuracion;
$description = $dataSett->descripcion_configuracion;
$keywords = $keywords;
$image = $faviSet;
$url = TemplateController::path();

?>

    <title>.:: <?php echo $title ?> ::.</title>
    <!-- Meta -->
    <meta name="description" content="<?php echo $description ?>">
    <meta name="keywords" content="<?php echo $keywords ?>">
    <meta name="author" content="<?php echo $dataSett->nombre_empresa_configuracion ?>">

    <!--=====================================
	Marcado OPEN GRAPH FACEBOOK
	======================================-->
	<meta property="og:site_name" content="<?php echo $name ?>">
	<meta property="og:title" content="<?php echo $title ?>">
	<meta property="og:description" content="<?php echo $description ?>">
	<meta property="og:type" content="Type">
	<meta property="og:image" content="<?php echo $image ?>">
	<meta property="og:url" content="<?php echo $url ?>">

	<!--=====================================
	Marcado TWITTER
	======================================-->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="@developer-technology">
	<meta name="twitter:creator" content="@developer-technology">
	<meta name="twitter:title" content="<?php echo $title ?>">
	<meta name="twitter:description" content="<?php echo $description ?>">
	<meta name="twitter:image" content="<?php echo $image ?>">
	<meta name="twitter:image:width" content="800">
	<meta name="twitter:image:height" content="418">
	<meta name="twitter:image:alt" content="<?php echo $description ?>">

	<!--=====================================
	Marcado GOOGLE
	======================================-->
	<meta itemprop="name" content="<?php echo $title ?>">
	<meta itemprop="url" content="<?php echo $url ?>">
	<meta itemprop="description" content="<?php echo $description ?>">
	<meta itemprop="image" content="<?php echo $image ?>">

    <!-- Base URL -->
    <base href="<?php echo TemplateController::path() ?>">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $faviSet ?>" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&amp;family=Ubuntu:wght@400;500;700&amp;display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="views/assets/css/mdb.css">
    <link rel="stylesheet" href="views/assets/css/bootstrap.min.css">
    <!-- Nifty CSS -->
    <link rel="stylesheet" href="views/assets/css/app.min.css">
    <link rel="stylesheet" href="views/assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="views/assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="views/assets/plugins/material-preloader/material-preloader.css">
    <link rel="stylesheet" href="views/assets/plugins/notie/notie.css">
    <link rel="stylesheet" href="views/assets/pages/loader.css">
    <link rel="stylesheet" href="views/assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="views/assets/plugins/fork-awesome/css/fork-awesome.css">
    <link rel="stylesheet" href="views/assets/plugins/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="views/assets/plugins/support/czm-chat-support.css">
    <link rel="stylesheet" href="views/assets/plugins/tags-input/tags-input.css">
    <link rel="stylesheet" href="views/assets/plugins/summernote/summernote-bs4.min.css">
    <link rel="stylesheet" href="views/assets/plugins/dropzone/dropzone.css">
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
    <!-- JAVASCRIPTS -->
    <script src="views/assets/plugins/jquery/jquery.min.js"></script>
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="views/assets/js/mdb.js"></script>
    <script src="views/assets/js/bootstrap.js"></script>
    <script src="views/assets/js/bootstrap.min.js"></script>
    <script src="views/assets/js/bootstrap.bundle.js"></script>
    <!-- JS -->
    <script src="views/assets/js/app.min.js"></script>
    <script src="views/assets/plugins/clipboard/clipboard.min.js"></script>
    <script src="views/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <script src="views/assets/plugins/select2/js/select2.full.min.js"></script>
    <script src="views/assets/plugins/material-preloader/material-preloader.js"></script>
    <script src="views/assets/plugins/notie/notie.min.js"></script>
    <script src="views/assets/plugins/sweet-alert/sweetalert2-10.js"></script>
    <script src="views/assets/custom/alerts/alerts.js"></script>
    <script src="views/assets/plugins/themify-icons/themify-icons.js"></script>

    <?php if (!empty($routesArray[1])): ?>

        <?php

/*=============================================
Ocupamos Datatable en las vistas
=============================================*/
if ($routesArray[1] == "users" ||
    $routesArray[1] == "tenants" ||
    $routesArray[1] == "profile" ||
    $routesArray[1] == "sales" ||
    $routesArray[1] == "despatch-remitent" ||
    $routesArray[1] == "despatch-transport" ||
    $routesArray[1] == "summary" ||
    $routesArray[1] == "voided" ||
    $routesArray[1] == "suscriptions" ||
    $routesArray[1] == "plans"):

?>

            <!-- DataTables  & Plugins -->
            <link rel="stylesheet" href="views/assets/plugins/daterangepicker/daterangepicker.css">
            <link rel="stylesheet" href="views/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
            <link rel="stylesheet" href="views/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
            <link rel="stylesheet" href="views/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

            <script src="views/assets/plugins/moment/moment.min.js"></script>
            <script src="views/assets/plugins/daterangepicker/daterangepicker.js"></script>
            <script src="views/assets/plugins/datatables/jquery.dataTables.min.js"></script>
            <script src="views/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
            <script src="views/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
            <script src="views/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
            <script src="views/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
            <script src="views/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
            <script src="views/assets/plugins/jszip/jszip.min.js"></script>
            <script src="views/assets/plugins/pdfmake/pdfmake.min.js"></script>
            <script src="views/assets/plugins/pdfmake/vfs_fonts.js"></script>
            <script src="views/assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
            <script src="views/assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
            <script src="views/assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

        <?php endif?>

    <?php else: ?>

        <!-- DataTables  & Plugins -->
        <link rel="stylesheet" href="views/assets/plugins/daterangepicker/daterangepicker.css">
        <link rel="stylesheet" href="views/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="views/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
        <link rel="stylesheet" href="views/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

        <script src="views/assets/plugins/moment/moment.min.js"></script>
        <script src="views/assets/plugins/daterangepicker/daterangepicker.js"></script>
        <script src="views/assets/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="views/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="views/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="views/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="views/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
        <script src="views/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
        <script src="views/assets/plugins/jszip/jszip.min.js"></script>
        <script src="views/assets/plugins/pdfmake/pdfmake.min.js"></script>
        <script src="views/assets/plugins/pdfmake/vfs_fonts.js"></script>
        <script src="views/assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
        <script src="views/assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
        <script src="views/assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

    <?php endif?>

    <script src="views/assets/plugins/support/components/moment/moment.min.js"></script>
    <script src="views/assets/plugins/support/components/moment/moment-timezone-with-data.min.js"></script>
    <script src="views/assets/plugins/support/czm-chat-support.min.js"></script>
    <script src="views/assets/plugins/tags-input/tags-input.js"></script>
    <script src="views/assets/plugins/summernote/summernote-bs4.min.js"></script>
    <script src="views/assets/plugins/dropzone/dropzone.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.9.7/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.9.7/plugin/customParseFormat.js"></script>
    <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
    <script src="views/assets/pages/chartjs.js" defer></script>

    <link rel="stylesheet" href="views/assets/css/custom.css">

</head>

<body class="rubber">

    <div id="divChat" class="hidden"></div>

    <?php

/*=============================================
Si no hay sesion de usuario
=============================================*/
if (!isset($_SESSION["user"])) {

    if (!empty($routesArray[1])) {

        if ($routesArray[1] == "forgot" ||
            $routesArray[1] == "verify" ||
            $routesArray[1] == "register") {

            include "views/pages/" . $routesArray[1] . "/" . $routesArray[1] . ".php";
            echo '</body></html>';
            return;

        } else {

            include "views/pages/404/f_404.php";
            echo '</body></html>';
            return;

        }

    } else {

        include "views/pages/login/login.php";
        echo '</body></html>';
        return;

    }

}

if (!empty($_SESSION["admin"]) && empty($_SESSION["empresa"])): ?>

    <?php if (!empty($routesArray[1])) {

    if ($routesArray[1] == "profile" ||
        $routesArray[1] == "users" ||
        $routesArray[1] == "plans" ||
        $routesArray[1] == "tenants" ||
        $routesArray[1] == "sales" ||
        $routesArray[1] == "settings" ||
        $routesArray[1] == "change" ||
        $routesArray[1] == "redirect" ||
        $routesArray[1] == "suscriptions" ||
        $routesArray[1] == "logout") {

        ?>

    <div id="root" class="root mn--max hd--expanded mn--sticky">

        <!-- CONTENTS -->
        <section id="content" class="content">

            <?php include "pages/" . $routesArray[1] . "/" . $routesArray[1] . ".php"?>
            <!-- FOOTER -->
            <?php include "views/modules/footer.php"?>
            <!-- END - FOOTER -->

        </section>

        <!-- END - CONTENTS -->

        <!-- HEADER -->
        <?php include "views/modules/admin/header.php"?>
        <!-- END - HEADER -->

        <!-- MAIN NAVIGATION -->
        <?php include "views/modules/admin/sidebar.php"?>
        <!-- END - MAIN NAVIGATION -->

    </div>

<?php } else {?>

        <div id="root" class="root mn--max hd--expanded mn--sticky">

            <!-- CONTENTS -->
            <section id="content" class="content">

                <?php include "views/pages/404/b_404.php"?>
                <!-- FOOTER -->
                <?php include "views/modules/footer.php"?>
                <!-- END - FOOTER -->

            </section>

            <!-- END - CONTENTS -->

            <!-- HEADER -->
            <?php include "views/modules/admin/header.php"?>
            <!-- END - HEADER -->

            <!-- MAIN NAVIGATION -->
            <?php include "views/modules/admin/sidebar.php"?>
            <!-- END - MAIN NAVIGATION -->

        </div>

<?php }

} else {?>

    <div id="root" class="root mn--max hd--expanded mn--sticky">

        <!-- CONTENTS -->
        <section id="content" class="content">

            <?php include "views/pages/admin/admin.php"?>
            <!-- FOOTER -->
            <?php include "views/modules/footer.php"?>
            <!-- END - FOOTER -->

        </section>

        <!-- END - CONTENTS -->

        <!-- HEADER -->
        <?php include "views/modules/admin/header.php"?>
        <!-- END - HEADER -->

        <!-- MAIN NAVIGATION -->
        <?php include "views/modules/admin/sidebar.php"?>
        <!-- END - MAIN NAVIGATION -->

    </div>

<?php } ?>

<?php endif?>

<?php

/*=============================================
Si hay sesion de usuario pero no de empresa
=============================================*/
if (isset($_SESSION["user"]) && empty($_SESSION["empresa"]) && empty($_SESSION["admin"])) {

    if (!empty($routesArray[1])) {

        if ($routesArray[1] == "logout" ||
            $routesArray[1] == "businesses" ||
            $routesArray[1] == "cart" ||
            $routesArray[1] == "redirect") {

            include "views/pages/" . $routesArray[1] . "/" . $routesArray[1] . ".php";
            echo '</body></head>';
            return;

        } else {

            include "views/pages/404/f_404.php";
            echo '</body></head>';
            return;

        }

    } else {

        include "views/pages/tenants/tenants.php";
        echo '</body></head>';
        return;

    }

}

?>

    <?php if (isset($_SESSION["user"]) && empty($_SESSION["admin"])): ?>
        <!-- PAGE CONTAINER -->
        <?php if (!empty($routesArray[1])) {

    if ($routesArray[1] == "logo" ||
        $routesArray[1] == "certificate" ||
        $routesArray[1] == "consult" ||
        $routesArray[1] == "profile" ||
        $routesArray[1] == "change" ||
        $routesArray[1] == "pay" ||
        $routesArray[1] == "businesses" ||
        $routesArray[1] == "despatch-remitent" ||
        $routesArray[1] == "despatch-transport" ||
        $routesArray[1] == "summary" ||
        $routesArray[1] == "voided" ||
        $routesArray[1] == "suscriptions" ||
        $routesArray[1] == "logout") {

        ?>

                <div id="root" class="root mn--max hd--expanded mn--sticky">

                    <!-- CONTENTS -->
                    <section id="content" class="content">

                        <?php include "pages/" . $routesArray[1] . "/" . $routesArray[1] . ".php"?>
                        <!-- FOOTER -->
                        <?php include "views/modules/footer.php"?>
                        <!-- END - FOOTER -->

                    </section>

                    <!-- END - CONTENTS -->

                    <!-- HEADER -->
                    <?php include "views/modules/header.php"?>
                    <!-- END - HEADER -->

                    <!-- MAIN NAVIGATION -->
                    <?php include "views/modules/sidebar.php"?>
                    <!-- END - MAIN NAVIGATION -->

                </div>

            <?php } else {?>

                    <div id="root" class="root mn--max hd--expanded mn--sticky">

                        <!-- CONTENTS -->
                        <section id="content" class="content">

                            <?php include "views/pages/404/b_404.php"?>
                            <!-- FOOTER -->
                            <?php include "views/modules/footer.php"?>
                            <!-- END - FOOTER -->

                        </section>

                        <!-- END - CONTENTS -->

                        <!-- HEADER -->
                        <?php include "views/modules/header.php"?>
                        <!-- END - HEADER -->

                        <!-- MAIN NAVIGATION -->
                        <?php include "views/modules/sidebar.php"?>
                        <!-- END - MAIN NAVIGATION -->

                    </div>

            <?php }

} else {?>

                <div id="root" class="root mn--max hd--expanded mn--sticky">

                    <!-- CONTENTS -->
                    <section id="content" class="content">

                        <?php include "views/pages/home/home.php"?>
                        <!-- FOOTER -->
                        <?php include "views/modules/footer.php"?>
                        <!-- END - FOOTER -->

                    </section>

                    <!-- END - CONTENTS -->

                    <!-- HEADER -->
                    <?php include "views/modules/header.php"?>
                    <!-- END - HEADER -->

                    <!-- MAIN NAVIGATION -->
                    <?php include "views/modules/sidebar.php"?>
                    <!-- END - MAIN NAVIGATION -->

                </div>

            <?php }?>

        <!-- END - PAGE CONTAINER -->
        <?php if (!empty($_SESSION["empresa"])) {

    /*=============================================
    Inlcuimos el modal de las credenciales
    =============================================*/
    include "views/modals/viewToken.php";

}?>

        <!-- SCROLL TO TOP BUTTON -->
        <?php include "views/modules/scroll.php"?>

    <?php endif?>

    <script src="views/assets/custom/forms/forms.js"></script>

</body>

</html>