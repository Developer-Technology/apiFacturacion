<?php

/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

require_once "../controllers/curl.controller.php";
require_once "../controllers/template.controller.php";

class AjaxGet
{

    public $data;
    public $table;
    public $suffix;

    public function dataGet()
    {

        $url = $this->table . "?select=*&linkTo=" . $this->suffix . "&equalTo=" . $this->data;

        $method = "GET";
        $fields = array();
        $token = TemplateController::tokenSet();

        $response = CurlController::requestSunat($url, $method, $fields, $token);

        echo json_encode($response->response);

    }

}

if (isset($_POST["data"])) {

    $validate = new AjaxGet();
    $validate->data = $_POST["data"];
    $validate->table = $_POST["table"];
    $validate->suffix = $_POST["suffix"];
    $validate->dataGet();

}