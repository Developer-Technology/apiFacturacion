<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Dejamos en blanco la sesion de la empresa
=============================================*/
//$_SESSION['empresa'] = "";
//$_SESSION['admin'] = "";
unset($_SESSION['admin']);
unset($_SESSION['empresa']);

/*=============================================
Redireccionamos
=============================================*/
echo '<script>
        fncFormatInputs();
        window.location = "/"
    </script>';