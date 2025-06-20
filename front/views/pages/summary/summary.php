<?php if($_SESSION["user"]->rol_usuario == 3) {

	echo '<script>
			fncFormatInputs();
			matPreloader("off");
			fncSweetAlert("close", "", "");
			fncSweetAlert("error", "You do not have permission to access this view", "/");
		</script>';

} else { ?>

<div class="content__header content__boxed overlapping">
    <div class="content__wrap">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">

                <li class="breadcrumb-item"><a href="/">Inicio</a></li>

                <?php if (isset($routesArray[2])): ?>

                    <?php if ($routesArray[2] == "new" || $routesArray[2] == "edit"): ?>

                        <li class="breadcrumb-item"><a href="/summary">Resumen De Boletas</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $txtBread ?></li>

                    <?php endif?>

                <?php else: ?>

                    <li class="breadcrumb-item active" aria-current="page">Resumen De Boletas</li>

                <?php endif?>

            </ol>
        </nav>
        <!-- END : Breadcrumb -->

        <h1 class="page-title mb-0 mt-2">Resumen De Boletas</h1>
        <p class="lead">En esta sección podrás realizar el envío de resumenes de boletas.</p>

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

<?php } ?>

    </div>

</div>