/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

var page;

function execDatatable(text) {

  /*=============================================
  Validamos tabla de planes
  =============================================*/
  if ($(".tablePlans").length > 0) {

    var url = "ajax/data-plans.php?text=" + text + "&between1=" + $("#between1").val() + "&between2=" + $("#between2").val() + "&token=" + localStorage.getItem("token_user");

    var columns = [
      { "data": "id_plan" },
      { "data": "nombre_plan" },
      { "data": "precio_plan" },
      { "data": "descripcion_plan" },
      { "data": "contiene_plan", "orderable": false },
      { "data": "creado_plan" },
      { "data": "actions", "orderable": false }
    ];

    page = "plans";

  }

  /*=============================================
  Validamos tabla de empresas
  =============================================*/
  if ($(".tableTenants").length > 0) {

    var url = "ajax/data-tenants.php?text=" + text + "&between1=" + $("#between1").val() + "&between2=" + $("#between2").val() + "&token=" + localStorage.getItem("token_user");

    var columns = [
      { "data": "id_empresa" },
      { "data": "estado_empresa", "orderable": false },
      { "data": "logo_empresa", "orderable": false },
      { "data": "ruc_empresa" },
      { "data": "fase_empresa" },
      { "data": "plan_empresa" },
      { "data": "consumo_plan" },
      { "data": "creado_empresa" },
      { "data": "acciones", "orderable": false }
    ];

    page = "tenants";

  }

  /*=============================================
  Validamos tabla de usuarios
  =============================================*/
  if ($(".tableUsers").length > 0) {

    var url = "ajax/data-users.php?text=" + text + "&between1=" + $("#between1").val() + "&between2=" + $("#between2").val() + "&token=" + localStorage.getItem("token_user");

    var columns = [
      { "data": "id_usuario" },
      { "data": "estado_usuario", "orderable": false },
      { "data": "datos_usuario" },
      { "data": "contacto_usuario" },
      { "data": "rol_usuario" },
      { "data": "metodo_usuario" },
      { "data": "verificado_usuario" },
      { "data": "empresas_usuario" },
      { "data": "creado_usuario" },
      { "data": "acciones", "orderable": false }
    ];

    page = "users";

  }

  /*=============================================
  Validamos tabla de ventas
  =============================================*/
  if ($(".tableSales").length > 0) {

    var url = "ajax/data-sales.php?text=" + text + "&between1=" + $("#between1").val() + "&between2=" + $("#between2").val() + "&token=" + localStorage.getItem("token_user");

    var columns = [
      { "data": "id_venta" },
      { "data": "trans_venta", "orderable": false },
      { "data": "nombre_plan" },
      { "data": "datos_compra", "orderable": false },
      { "data": "estado_venta" },
      { "data": "asignado_venta" },
      { "data": "creado_venta" }
    ];

    page = "sales";

  }

  /*=============================================
  Validamos tabla de suscripciones
  =============================================*/
  if ($(".tableSuscription").length > 0) {

    var url = "ajax/data-suscriptions.php?text=" + text + "&between1=" + $("#between1").val() + "&between2=" + $("#between2").val() + "&token=" + localStorage.getItem("token_user") + "&idTenant=" + $("#idTenant").val();

    var columns = [
      { "data": "id_suscripcion" },
      { "data": "fecha_emision" },
      { "data": "fecha_pago" },
      { "data": "datos_compra", "orderable": false },
      { "data": "monto_pago" },
      { "data": "plan_suscripcion", "orderable": false },
      { "data": "estado_suscripcion" },
      { "data": "descargar_suscripcion", "orderable": false },
      { "data": "acciones", "orderable": false },
      { "data": "pago_suscripcion" }
    ];

    page = "suscriptions";

  }

  /*=============================================
  Validamos tabla guia de remision remitente
  =============================================*/
  if ($(".tableDtpub").length > 0) {

    var url = "ajax/data-remitent.php?text=" + text + "&between1=" + $("#between1").val() + "&between2=" + $("#between2").val() + "&token=" + localStorage.getItem("token_user") + "&idTenant=" + $("#idTenant").val();

    var columns = [
      { "data": "id_despatch" },
      { "data": "doc_despatch" },
      { "data": "tipo_despatch" },
      { "data": "emision_despatch" },
      { "data": "baja_despatch", "orderable": false },
      { "data": "rel_despatch", "orderable": false },
      { "data": "dest_despatch", "orderable": false },
      { "data": "estado_despatch" },
      { "data": "creado_despatch" },
      { "data": "actions", "orderable": false }
    ];

    page = "despatch-remitent";

  }

  /*=============================================
  Validamos tabla guia de remision transportista
  =============================================*/
  if ($(".tableDtrans").length > 0) {

    var url = "ajax/data-transportist.php?text=" + text + "&between1=" + $("#between1").val() + "&between2=" + $("#between2").val() + "&token=" + localStorage.getItem("token_user") + "&idTenant=" + $("#idTenant").val();

    var columns = [
      { "data": "id_transportist" },
      { "data": "doc_transportist" },
      { "data": "emision_transportist" },
      { "data": "dest_transportist", "orderable": false },
      { "data": "estado_transportist" },
      { "data": "creado_transportist" },
      { "data": "actions", "orderable": false }
    ];

    page = "despatch-transport";

  }

  /*=============================================
  Validamos tabla resumen diario
  =============================================*/
  if ($(".tableSumm").length > 0) {

    var url = "ajax/data-summary.php?text=" + text + "&between1=" + $("#between1").val() + "&between2=" + $("#between2").val() + "&token=" + localStorage.getItem("token_user") + "&idTenant=" + $("#idTenant").val();

    var columns = [
      { "data": "id_summary" },
      { "data": "doc_summary" },
      { "data": "emision_summary" },
      { "data": "estado_summary" },
      { "data": "creado_summary" },
      { "data": "actions", "orderable": false }
    ];

    page = "summary";

  }

  /*=============================================
  Validamos tabla resumen diario
  =============================================*/
  if ($(".tableVoid").length > 0) {

    var url = "ajax/data-voided.php?text=" + text + "&between1=" + $("#between1").val() + "&between2=" + $("#between2").val() + "&token=" + localStorage.getItem("token_user") + "&idTenant=" + $("#idTenant").val();

    var columns = [
      { "data": "id_voided" },
      { "data": "doc_voided" },
      { "data": "emision_voided" },
      { "data": "estado_voided" },
      { "data": "creado_voided" },
      { "data": "actions", "orderable": false }
    ];

    page = "voided";

  }

  /*=============================================
  Ejecutamos DataTable
  =============================================*/
  var adminsTable = $("#adminsTable").DataTable({

    "responsive": true,
    "lengthChange": true,
    "aLengthMenu": [[5, 10, 50,50, 500, 1000], [5, 10, 50, 100, 500, 1000]],
    "autoWidth": false,
    "processing": true,
    "serverSide": true,
    "order": [[0, "desc"]],
    "ajax": {
      "url": url,
      "type": "POST"
    },
    "columns": columns,
    "language": {

      "sProcessing": "Procesando...",
      "sLengthMenu": "Mostrar _MENU_ registros",
      "sZeroRecords": "No se encontraron resultados",
      "sEmptyTable": "Ningún dato disponible en esta tabla",
      "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
      "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0",
      "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
      "sInfoPostFix": "",
      "sSearch": "Buscar:",
      "sUrl": "",
      "sInfoThousands": ",",
      "sLoadingRecords": "Cargando...",
      "oPaginate": {
        "sFirst": "Primero",
        "sLast": "Último",
        "sNext": "Siguiente",
        "sPrevious": "Anterior"
      },
      "oAria": {
        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
      }

    },

    "buttons": [

      { extend: "copy", className: "btn-dark" },
      { extend: "csv", className: "btn-dark" },
      { extend: "excel", className: "btn-dark" },
      { extend: "pdf", className: "btn-dark", orientation: "landscape" },
      { extend: "print", className: "btn-dark" }

    ],
    fnDrawCallback: function (oSettings) {
      if (oSettings.aoData.length == 0) {
        $('.dataTables_paginate').hide();
        $('.dataTables_info').hide();
      }

    }
  })

  if (text == "flat") {

    $("#adminsTable").on("draw.dt", function () {

      setTimeout(function () {

        adminsTable.buttons().container().appendTo('#adminsTable_wrapper .col-md-6:eq(0)');

      }, 100)

    })

  }

};

execDatatable("html");

/*=============================================
Ejecutar reporte 
=============================================*/
function reportActive(event) {

  if (event.target.checked) {

    $("#adminsTable").dataTable().fnClearTable();
    $("#adminsTable").dataTable().fnDestroy();

    setTimeout(function () {

      execDatatable("flat");

    }, 100)

  } else {

    $("#adminsTable").dataTable().fnClearTable();
    $("#adminsTable").dataTable().fnDestroy();

    setTimeout(function () {

      execDatatable("html");

    }, 100)
  }

}

/*=============================================
Rango de fechas
=============================================*/
$('#daterange-btn').daterangepicker(
  {
    "locale": {
      "format": "YYYY-MM-DD",
      "separator": " - ",
      "applyLabel": "Aplicar",
      "cancelLabel": "Cancelar",
      "fromLabel": "Desde",
      "toLabel": "Hasta",
      "customRangeLabel": "Rango Personalizado",
      "daysOfWeek": [
        "Do",
        "Lu",
        "Ma",
        "Mi",
        "Ju",
        "Vi",
        "Sa"
      ],
      "monthNames": [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre"
      ],
      "firstDay": 1
    },
    ranges: {
      'Hoy': [moment(), moment()],
      'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
      'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
      'Este Mes': [moment().startOf('month'), moment().endOf('month')],
      'Último Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
      'Este Año': [moment().startOf('year'), moment().endOf('year')],
      'Último Año': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
    },

    startDate: moment($("#between1").val()),
    endDate: moment($("#between2").val())

  },
  function (start, end) {

    window.location = page+"?start="+start.format('YYYY-MM-DD')+"&end="+end.format('YYYY-MM-DD');

  },

)

/*=============================================
Eliminar registro
=============================================*/
$(document).on("click", ".removeItem", function () {

  var idItem = $(this).attr("idItem");
  var table = $(this).attr("table");
  var suffix = $(this).attr("suffix");
  var folder = $(this).attr("folder");
  var code = $(this).attr("code");
  var deleteFile = $(this).attr("deleteFile");
  var page = $(this).attr("page");

  fncSweetAlert("confirm", "Are you sure to delete this record?", "").then(resp => {

    if (resp) {

      var data = new FormData();
      data.append("idItem", idItem);
      data.append("table", table);
      data.append("suffix", suffix);
      data.append("folder", folder);
      data.append("code", code);
      data.append("token", localStorage.getItem("token_user"));
      data.append("deleteFile", deleteFile);

      $.ajax({

        url: "ajax/ajax-delete.php",
        method: "POST",
        data: data,
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {

          if (response == 200) {

            fncSweetAlert(
              "success",
              "The record has been successfully deleted",
              "/" + page
            );

          } else if (response == "no-delete") {

            fncSweetAlert(
              "error",
              "The record has related data",
              "/" + page
            );

          } else {

            fncNotie(3, "Failed to delete record");

          }

        }

      })

    }

  })

})

/*=============================================
Cambiar estado del producto
=============================================*/
function changeState(event, id, table, suffix) {

  if (event.target.checked) {

    var state = "1";

  } else {

    var state = "0";

  }

  var data = new FormData();
  data.append("state", state);
  data.append("id", id);
  data.append("table", table);
  data.append("suffix", suffix);
  data.append("token", localStorage.getItem("token_user"));


  $.ajax({
    url: "ajax/ajax-state.php",
    method: "POST",
    data: data,
    contentType: false,
    cache: false,
    processData: false,
    success: function (response) {

      if (response == 200) {

        fncNotie(1, "The record was updated");

      } else {

        fncNotie(3, "Failed to update record");

      }

    }

  })

}

/*=============================================
Función para actualizar la orden
=============================================*/
$(document).on("click", ".nextProcess", function () {

  /*=============================================
  Limpiamos la ventana modal
  =============================================*/
  $(".orderBody").html("");

  var idOrder = $(this).attr("idOrder");
  var processOrder = JSON.parse(atob($(this).attr("processOrder")));


  /*=============================================
  Nombramos la ventana modal con el id de la orden
  =============================================*/
  $(".modal-title span").html("Order N. " + idOrder);

  /*=============================================
   Quitamos la opción de llenar el campo de recibido si no se ha enviado el producto
  =============================================*/
  if (processOrder[1].status == "pending") {

    processOrder.splice(2, 1);

  }

  /*=============================================
  Información dinámica que aparecerá en la ventana modal
  =============================================*/
  processOrder.forEach((value, index) => {

    let date = "";
    let status = "";
    let comment = "";

    if (value.status == "ok") {

      date = `<div class="col-10 p-3">
          
              <input type="date" class="form-control" value="`+ value.date + `" readonly>

          </div>`;

      status = `<div class="col-10 mt-1 p-3">

                <div class="text-uppercase">`+ value.status + `</div>

              </div>`;

      comment = `<div class="col-10 p-3">   
                <textarea class="form-control" readonly>`+ value.comment + `</textarea>
            </div>`;

    } else {

      date = `<div class="col-10 p-3">
          
              <input type="date" class="form-control" name="date" value="`+ value.date + `" required>

          </div>`;


      status = `<div class="col-10 mt-1 p-3">

                    <input type="hidden" name="stage" value="`+ value.stage + `">
                    <input type="hidden" name="processOrder" value="`+ $(this).attr("processOrder") + `">
                    <input type="hidden" name="idOrder" value="`+ idOrder + `">
                    <input type="hidden" name="clientOrder" value="`+ $(this).attr("clientOrder") + `">
                    <input type="hidden" name="emailOrder" value="`+ $(this).attr("emailOrder") + `">
                    <input type="hidden" name="productOrder" value="`+ $(this).attr("productOrder") + `">

                    <div class="custom-control custom-radio custom-control-inline">

                      <input 
                          id="status-pending" 
                          type="radio" 
                          class="custom-control-input" 
                          value="pending" 
                          name="status" 
                          checked>

                          <label  class="custom-control-label" for="status-pending">Pending</label>

                    </div>

                    <div class="custom-control custom-radio custom-control-inline">

                      <input 
                          id="status-ok" 
                          type="radio" 
                          class="custom-control-input" 
                          value="ok" 
                          name="status" 
                          >

                          <label  class="custom-control-label" for="status-ok">Ok</label>

                    </div>

        </div>`;

      comment = `<div class="col-10 p-3">   
                <textarea class="form-control" name="comment" required>`+ value.comment + `</textarea>
            </div>`;

    }


    $(".orderBody").append(`

       <div class="card-header text-uppercase">`+ value.stage + `</div> 

       <div class="card-body">
          
          <!--=====================================
          Bloque Fecha
          ======================================-->

          <div class="form-row">

            <div class="col-2 text-right">

                <label class="p-3 lead">Date:</label>

            </div>

            `+ date + `

          </div>

          <!--=====================================
          Bloque Status
          ======================================-->

          <div class="form-row">
                        
            <div class="col-2 text-right">
                <label class="p-3 lead">Status:</label>
            </div>

            `+ status + `

          </div> 

          <!--=====================================
            Bloque Comentarios
          ======================================-->

          <div class="form-row">

            <div class="col-2 text-right">
                <label class="p-3 lead">Comment:</label>
            </div>

            `+ comment + `

          </div>

        </div>
     

    `)

  })

  $("#nextProcess").modal()

})

/*=============================================
Función para responder disputa
=============================================*/
$(document).on("click", ".answerDispute", function () {

  $("[name='idDispute']").val($(this).attr("idDispute"));
  $("[name='clientDispute']").val($(this).attr("clientDispute"));
  $("[name='emailDispute']").val($(this).attr("emailDispute"));

  /*=============================================
   Aparecemos la ventana Modal
   =============================================*/
  $("#answerDispute").modal()

})

/*=============================================
Función para responder mensaje
=============================================*/
$(document).on("click", ".answerMessage", function () {

  $("[name='idMessage']").val($(this).attr("idMessage"));
  $("[name='clientMessage']").val($(this).attr("clientMessage"));
  $("[name='emailMessage']").val($(this).attr("emailMessage"));
  $("[name='urlProduct']").val($(this).attr("urlProduct"));

  /*=============================================
 Aparecemos la ventana Modal
 =============================================*/
  $("#answerMessage").modal()

})