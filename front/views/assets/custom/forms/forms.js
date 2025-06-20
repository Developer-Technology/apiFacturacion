/*-------------------------
Autor: Chanamoth
Web: www.chanamoth.com
Mail: info@chanamoth.com
---------------------------*/

/*=============================================
Validación desde Bootstrap 4
=============================================*/
(function () {

  'use strict';

  window.addEventListener('load', function () {

    var forms = document.getElementsByClassName('needs-validation');

    var validation = Array.prototype.filter.call(forms, function (form) {

      form.addEventListener('submit', function (event) {

        if (form.checkValidity() === false) {

          event.preventDefault();
          event.stopPropagation();

        }

        form.classList.add('was-validated');

      }, false);

    });

  }, false);

})();

/*=============================================
Activación de Select 2
=============================================*/
$('.select2').select2({
  theme: 'bootstrap4'
})

/*=============================================
Activación de Select 2 con minimo
=============================================*/
$('.select2-search').select2({
  theme: 'bootstrap4',
  minimumInputLength: 2
})

/*=============================================
Centrar ventana
=============================================*/
function VentanaCentrada(theURL, winName, features, myWidth, myHeight, isCenter) { //v3.0
  if (window.screen) if (isCenter) if (isCenter == "true") {
    var myLeft = (screen.width - myWidth) / 2;
    var myTop = (screen.height - myHeight) / 2;
    features += (features != '') ? ',' : '';
    features += ',left=' + myLeft + ',top=' + myTop;
  }
  window.open(theURL, winName, features + ((features != '') ? ',' : '') + 'width=' + myWidth + ',height=' + myHeight);
}

/*=============================================
Función para validar data repetida
=============================================*/
function validateRepeat(event, type, table, suffix) {

  var data = new FormData();
  data.append("data", event.target.value);
  data.append("table", table);
  data.append("suffix", suffix);

  $.ajax({
    url: "ajax/ajax-validate.php",
    method: "POST",
    data: data,
    contentType: false,
    cache: false,
    processData: false,
    success: function (response) {

      if (response == 200) {

        event.target.value = "";
        $(event.target).parent().addClass("was-validated");
        $(event.target).parent().children(".invalid-feedback").html("The data entered is already registered in the database");

        fncNotie(3, "The data entered is already registered in the database");

      } else {

        validateJS(event, type);

        if (table == "categories" || table == "subcategories" || table == "stores" || table == "products" || table == "brands") {

          createUrl(event, "url-" + suffix);

        }

      }

    }

  })

}

/*=============================================
Función para validar formulario
=============================================*/
function validateJS(event, type) {

  var pattern;

  if (type == "text") pattern = /^[A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,}$/;

  if (type == "text&number") pattern = /^[0-9A-Za-zñÑáéíóúÁÉÍÓÚ ]{1,50}$/;

  if (type == "numbers") pattern = /^[.\\,\\0-9]{1,}$/;

  if (type == "t&n") pattern = /^[A-Za-z0-9]{1,}$/;

  if (type == "email") pattern = /^[.a-zA-Z0-9_]+([.][.a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/;

  if (type == "pass") pattern = /^[#\\=\\$\\;\\*\\_\\?\\¿\\!\\¡\\:\\.\\,\\0-9a-zA-Z]{1,}$/;

  if (type == "regex") pattern = /^[-\\(\\)\\=\\%\\&\\$\\;\\_\\*\\"\\#\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]{1,}$/;

  if (type == "icon") {

    pattern = /^[-\\(\\)\\=\\%\\&\\$\\;\\_\\*\\"\\#\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]{1,}$/;

    $(".viewIcon").html('<i class="' + event.target.value + '"></i>')

  }

  if (type == "phone") pattern = /^[-\\(\\)\\0-9 ]{1,}$/;

  /*if (!pattern.test(event.target.value)) {

    $(event.target).parent().addClass("was-validated");
    $(event.target).parent().children(".invalid-feedback").html("Field syntax error");

  }*/

}

/*=============================================
Validamos certificado digital
=============================================*/
function validatePfx(event) {

  var image = event.target.files[0];

  if (image["type"] !== "application/x-pkcs12") {

    fncNotie(3, "El formato no es válido, debe ser .pfx");
    event.target.value = '';

    return;

  }

}

/*=============================================
Validamos imagen
=============================================*/
function validateImageJS(event, input) {

  var image = event.target.files[0];

  if (image["type"] !== "image/png" && image["type"] !== "image/jpeg" && image["type"] !== "image/gif") {

    fncNotie(3, "La imagen debe estar en formato JPG, PNG o GIF");
    event.target.value = '';

    return;

  }

  else if (image["size"] > 2000000) {

    fncNotie(3, "La imagen no debe pesar más de 2 MB");
    event.target.value = '';

    return;

  } else {

    var data = new FileReader();
    data.readAsDataURL(image);

    $(data).on("load", function (event) {

      var path = event.target.result;

      $("." + input).attr("src", path);

    })

  }

}

/*=============================================
Función para seleccionar color del calendario
=============================================*/
function changeChose(value) {

  $("#valueChose").val(value);

  if (value == "blue") {
    $(".fas").removeClass("chose__active");
    $(".cblue").addClass("chose__active");
  }

  if (value == "indigo") {
    $(".fas").removeClass("chose__active");
    $(".cindigo").addClass("chose__active");
  }

  if (value == "purple") {
    $(".fas").removeClass("chose__active");
    $(".cpurple").addClass("chose__active");
  }

  if (value == "pink") {
    $(".fas").removeClass("chose__active");
    $(".cpink").addClass("chose__active");
  }

  if (value == "red") {
    $(".fas").removeClass("chose__active");
    $(".cred").addClass("chose__active");
  }

  if (value == "orange") {
    $(".fas").removeClass("chose__active");
    $(".corange").addClass("chose__active");
  }

  if (value == "yellow") {
    $(".fas").removeClass("chose__active");
    $(".cyellow").addClass("chose__active");
  }

  if (value == "green") {
    $(".fas").removeClass("chose__active");
    $(".cgreen").addClass("chose__active");
  }

  if (value == "teal") {
    $(".fas").removeClass("chose__active");
    $(".cteal").addClass("chose__active");
  }

  if (value == "cyan") {
    $(".fas").removeClass("chose__active");
    $(".ccyan").addClass("chose__active");
  }

  if (value == "gray") {
    $(".fas").removeClass("chose__active");
    $(".cgray").addClass("chose__active");
  }

  if (value == "gray-dark") {
    $(".fas").removeClass("chose__active");
    $(".cgray-dark").addClass("chose__active");
  }

}

/*=============================================
Función para recordar credenciales de ingreso
=============================================*/
function rememberMe(event) {

  if (event.target.checked) {

    localStorage.setItem("userRemember", $('[name="loginEmail"]').val());
    localStorage.setItem("checkRemember", true);

  } else {

    localStorage.removeItem("userRemember");
    localStorage.removeItem("checkRemember");

  }

}

$(document).on("click", "._dm-clipboard", function () {

  fncNotie(1, "Valor copiado al portapapeles.");

})

/*=============================================
Eliminar evento
=============================================*/
$(document).on("click", ".removeEvent", function () {

  var idItem = $(this).attr("idItem");
  var table = $(this).attr("table");
  var suffix = $(this).attr("suffix");
  var folder = $(this).attr("folder");
  var code = $(this).attr("code");
  var deleteFile = $(this).attr("deleteFile");
  var page = $(this).attr("page");

  fncSweetAlert("confirm", "¿Estás seguro de eliminar este registro?", "").then(resp => {

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
              "El registro ha sido eliminado con éxito",
              "/" + page
            );

          } else if (response == "no-delete") {

            fncSweetAlert(
              "error",
              "El registro tiene datos relacionados.",
              "/" + page
            );

          } else {

            fncNotie(3, "Error al eliminar el registro");
            console.log(response);

          }

        }

      })

    }

  })

})

/*=============================================
Capturar el email para login desde el LocalStorage
=============================================*/
$(document).ready(function () {

  if (localStorage.getItem("userRemember") != null) {

    $('[name="loginEmail"]').val(localStorage.getItem("userRemember"));
  }

  if (localStorage.getItem("checkRemember") != null && localStorage.getItem("checkRemember")) {

    $('#remember').attr("checked", true);

  }

})

/*=============================================
Activación de Bootstrap Switch
=============================================*/
$("input[data-bootstrap-switch]").each(function () {

  $(this).bootstrapSwitch('state', $(this).prop('checked'));

})

/*=============================================
Función Preload
=============================================*/
function preload() {

  var preloadFalse = $(".preloadFalse");
  var preloadTrue = $(".preloadTrue");

  if (preloadFalse.length > 0) {
    preloadFalse.each(function (i) {
      var el = $(this);

      $(el).ready(function () {
        $(preloadTrue[i]).delay(1000).fadeOut();

        setTimeout(() => {
          $(preloadTrue[i]).remove(), $(el).css({ display: 'block' });
        }, 1000);

        setTimeout(() => {
          $(el).css({ height: 'auto' });
          $(el).animate({ display: "block", height: "auto" })
        }, 1001);
      });
    });
  }

}

/*=============================================
Función para crear Url's
=============================================*/
function createUrl(event, name) {

  var value = event.target.value;
  value = value.toLowerCase();
  value = value.replace(/[#\\;\\$\\&\\%\\=\\(\\)\\:\\,\\.\\¿\\¡\\!\\?\\]/g, "");
  value = value.replace(/[ ]/g, "_");
  value = value.replace(/[á]/g, "a");
  value = value.replace(/[é]/g, "e");
  value = value.replace(/[í]/g, "i");
  value = value.replace(/[ó]/g, "o");
  value = value.replace(/[ú]/g, "u");
  value = value.replace(/[ñ]/g, "n");

  $('[name="' + name + '"]').val(value);

}

/*=============================================
Tags Input
=============================================*/
function fncTagInput() {

  if ($('.tags-input').length > 0) {

    $('.tags-input').tagsinput({
      maxTags: 15
    });

  }

}

fncTagInput();

/*=============================================
Capturamos el método de pago
=============================================*/
var methodPaid = $('[name="payment-method"]').val();

function changeMethodPaid(event) {

  methodPaid = event.target.value;

}

/*=============================================
Funcion Get
=============================================*/
function funcGet(event, table, id, suffix) {

  var data = new FormData();
  data.append("data", id);
  data.append("table", table);
  data.append("suffix", suffix);

  $.ajax({
    url: "ajax/ajax-get.php",
    method: "POST",
    dataType: "json",
    data: data,
    contentType: false,
    cache: false,
    processData: false,

    beforeSend: function (objeto) {

      matPreloader("on");
      fncSweetAlert("loading", "Cargando...", "");

    },

    success: function (response) {

      matPreloader("off");
      fncSweetAlert("close", "", "");

      if (response.status == 200) {

        if (table == 'planes') {

          $('.idText').hide();
          var tipoCambio = $("#tipo-cambio").val();

          if (methodPaid == 'paypal') {

            $('.totalOrder').attr('total', (response.data[0].precio_plan / tipoCambio).toFixed(2));
            $('.totalOrder').text('$ ' + (response.data[0].precio_plan / tipoCambio).toFixed(2) + ' (USD)');
            $('#mon-sale').val('USD');

          }

          if ($(".totalOrder").attr("total") > 0) {

            $("#payMent").removeClass("hidden");
      
          } else {
      
            $("#payMent").addClass("hidden");
      
          }

          $('#plan-tenant').val(id);
          $('#plan-sale').val(response.data[0].ventas_plan);
          $('#plan-name').val(response.data[0].nombre_plan);
          $('.namePlan').text('Plan: ' + response.data[0].nombre_plan);
          $('.text_' + id).show();
          $('.button_next').show();

        }

      }

    }

  })

}

/*=============================================
Funcion Put
=============================================*/
function funcPut(event, table, id, suffix, dataUp) {

  var data = new FormData();
  data.append("data", id);
  data.append("table", table);
  data.append("suffix", suffix);
  data.append("dataUp", dataUp);

  $.ajax({
    url: "ajax/ajax-put.php",
    method: "POST",
    dataType: "json",
    data: data,
    contentType: false,
    cache: false,
    processData: false,

    success: function (response) { }

  })

}

/*=============================================
Funcion Post
=============================================*/
function funcPost(event, table, dataPost) {

  var data = new FormData();
  data.append("table", table);
  data.append("dataPost", dataPost);

  $.ajax({
    url: "ajax/ajax-post.php",
    method: "POST",
    dataType: "json",
    data: data,
    contentType: false,
    cache: false,
    processData: false,

    success: function (response) {}

  })

}

/*=============================================
Funcion para generar un código único aleatorio
=============================================*/
function generarCodigoUnico(longitud) {
  var caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  var codigo = '';
  for (var i = 0; i < longitud; i++) {
      codigo += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
  }
  return codigo;
}

/*=============================================
Función para procesar el checkout
=============================================*/
function checkout(type) {

  var forms = document.getElementsByClassName('needs-validation');

  var validation = Array.prototype.filter.call(forms, function (form) {

    if (form.checkValidity()) {

      return [""];
    }

  })

  if (validation.length > 0) {

    /*=============================================
    Pasarela de pago Paypal
    =============================================*/
    if (methodPaid == "paypal") {

      if ($(".totalOrder").attr("total") > 0) {

        /*=============================================
        Abrimos ventana modal para incorporar el botón de pago de Paypal
        =============================================*/
        fncSweetAlert("html", `<div id="paypal-button-container"></div>`, null);

        /*=============================================
        Declaramos función de paypal
        =============================================*/
        paypal.Buttons({

          createOrder: function (data, actions) {
            // This function sets up the details of the transaction, including the amount and line item details.
            return actions.order.create({
              purchase_units: [{
                amount: {
                  value: $(".totalOrder").attr("total")
                }
              }]
            });
          },

          onApprove: function (data, actions) {

            // This function captures the funds from the transaction.
            return actions.order.capture().then(function (details) {

              if (details.status == "COMPLETED") {

                //Generar la orden en la Base de datos
                //fncSweetAlert("success", `The transaction was completed successfully, ID: ` + details.id, "/businesses");
                newOrder("paypal", "pagado", details.id, $(".totalOrder").attr("total"));

              }

              return false;

            });

          },

          onCancel: function (data) {

            fncSweetAlert("error", `The transaction has been canceled`, null);

            return false;

          },

          onError: function (err) {

            fncSweetAlert("error", `An error occurred while making the transaction`, null);

            return false;

          }


        }).render('#paypal-button-container');

      } else {

        var codigoUnico = "free~" + generarCodigoUnico(10);
        var idPay = btoa(codigoUnico)
        newOrder("paypal", "pagado", idPay, $(".totalOrder").attr("total"));

      }

    }

    return false;

  } else {

    return false;

  }

}

/*=============================================
Funcion para ingresar el id de compra
=============================================*/
function gotoCreate() {

  /*=============================================
  Capturar el id del plan
  =============================================*/
  var idProduct = $("#plan-tenant").val();

  /*=============================================
  Capturar el id del usuario
  =============================================*/
  var idUser = $("#idUser").val();

  /*=============================================
  Capturar el id de la compra
  =============================================*/
  var idPayment = $("#idSale").val();

  if (idPayment != '') {

    var urlRedirect = btoa(idProduct + "~" + localStorage.getItem("token_user") + "~" + idPayment + "~" + idUser);

    window.location = "/businesses/" + urlRedirect;

  } else {

    fncNotie(3, "Please enter a value");
    $("#idSale").focus();

  }

}

/*=============================================
Crear la orden
=============================================*/
function newOrder(methodPaid, statusPaid, idPayment, total) {

  /*=============================================
  Capturar el id del usuario
  =============================================*/
  var idUser = $("#idUser").val();

  /*=============================================
  Capturar el id del plan
  =============================================*/
  var idProduct = $("#plan-tenant").val();

  /*=============================================
  Capturar la moneda
  =============================================*/
  var idMon = $("#mon-sale").val();

  /*=============================================
  Capturar las ventas del plan
  =============================================*/
  var salesProduct = $("#plan-sale").val();
  // Convertir el valor a un número
  const numeroSale = parseFloat(salesProduct);
  // Realizar la suma
  const resultadoSale = numeroSale + 1;

  /*=============================================
  Crear la descripción de la compra
  =============================================*/
  var description = $("#plan-name").val();

  if (methodPaid == "paypal") {

    /*=============================================
    Aumentar venta del plan
    =============================================*/
    var dataUp = "ventas_plan=" + resultadoSale;

    funcPut(event, 'planes', idProduct, 'id_plan', dataUp);

    /*=============================================
    Crear la venta en base de datos
    =============================================*/
    var dataPost = "id_usuario_venta=" + idUser + "&id_plan_venta=" + idProduct + "&metodo_venta=" + methodPaid + "&trans_venta=" + idPayment + "&moneda_venta=" + idMon + "&monto_venta=" + total + "&tipo_cambio_venta=" + $("#tipo-cambio").val() + "&estado_venta=" + statusPaid + "&creado_venta=" + formatDate(new Date());

    funcPost(event, 'ventas', dataPost);

    /*=============================================
    Cuando finaliza la creación de ventas con Paypal
    =============================================*/
    var urlRedirect = btoa(idProduct + "~" + localStorage.getItem("token_user") + "~" + idPayment + "~" + idUser);
    fncSweetAlert("success", "The purchase has been executed successfully", "/businesses/" + urlRedirect);

    return;

  }

}

/*=============================================
Plugin Summernote
=============================================*/
$(".summernote").summernote({

  placeholder: '',
  tabsize: 2,
  height: 100,
  toolbar: [
    ['misc', ['codeview', 'undo', 'redo']],
    ['style', ['bold', 'italic', 'underline', 'clear']],
    ['para', ['style', 'ul', 'ol', 'paragraph', 'height']],
    ['insert', ['link', 'picture', 'hr']]
  ]

});

/*=============================================
Adicionar Entradas al formulario de productos 
=============================================*/
function addInput(elem, type) {

  var inputs = $("." + type);

  if (inputs.length < 999999) {

    if (type == "inputSummary") {

      $(elem).before(`
         
        <div class="input-group mb-3 inputSummary">
                   
           <div class="input-group-append">
             <span class="input-group-text">
               <button type="button" class="btn btn-white btn-xs border-0" onclick="removeInput(`+ inputs.length + `,'inputSummary')">&times;</button>
             </span>
           </div>

          <input
              class="form-control" 
              type="text"
              name="summary-product_`+ inputs.length + `"
              pattern='[-\\(\\)\\=\\%\\&\\$\\;\\_\\*\\"\\#\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]{1,}'
              onchange="validateJS(event,'regex')"
              required>

        </div>


      `)

    }

    if (type == "inputDetails") {

      $(elem).before(`
         
        <div class="row mb-3 inputDetails">

          <div class="col-md-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text bg-danger">
                  <button type="button" class="btn btn-danger btn-xs border-0" onclick="removeInput(`+ inputs.length + `,'inputDetails')"><i class="fa fa-trash"></i></button>
                </span>
              </div>

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Und:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-un-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Cod:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-cod-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-6">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Desc:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-desc-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Cant:
                </span>
              </div>

              <input
              class="form-control" 
              type="number"
              name="item-cant-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

        </div>

      `)

    }

    if (type == "inputSpecifications") {

      $(elem).before(`
         
       <div class="row mb-3 inputSpecifications">

          <div class="col-12 col-lg-6 input-group">
                 
             <div class="input-group-append">
               <span class="input-group-text">
                 <button type="button" class="btn btn-white btn-xs border-0" onclick="removeInput(`+ inputs.length + `,'inputSpecifications')">&times;</button>
               </span>
             </div>

             <div class="input-group-append">
               <span class="input-group-text group__0">
                 Tipo:
               </span>
             </div>

            <input
            class="form-control" 
            type="text"
            name="spec-type-product_`+ inputs.length + `"
            pattern='[-\\(\\)\\=\\%\\&\\$\\;\\_\\*\\"\\#\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]{1,}'
            onchange="validateJS(event,'regex')"
            >

          </div>

          <div class="col-12 col-lg-6 input-group mt-2">
           
            <input
            class="form-control tags-input" 
            type="text"
            name="spec-value-product_`+ inputs.length + `"
            pattern='[-\\(\\)\\=\\%\\&\\$\\;\\_\\*\\"\\#\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]{1,}'
            onchange="validateJS(event,'regex')"
            >

          </div>

        </div>

      `)

      fncTagInput();

    }

    if (type == "inputSocial") {

      $(elem).before(`
         
        <div class="row mb-3 inputSocial">

            <div class="col-12 col-lg-6 input-group">
           
             <div class="input-group-append">
               <span class="input-group-text">
                 <button type="button" class="btn btn-white btn-xs border-0" onclick="removeInput(`+ inputs.length + `,'inputSocial')">&times;</button>
               </span>
             </div>

             <div class="input-group-append">
               <span class="input-group-text group__0">
                 URL:
               </span>
             </div>

            <input
                class="form-control" 
                type="text"
                name="social-url-tenant_`+ inputs.length + `"
                pattern='[-\\(\\)\\=\\%\\&\\$\\;\\_\\*\\"\\#\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]{1,}'
                onchange="validateJS(event,'regex')"
                required>

              </div>

              <div class="col-12 col-lg-6 input-group mt-2">
           
                <div class="input-group-append">
                  <span class="input-group-text">
                    Icono:
                  </span>
                </div>

                <input
                class="form-control" 
                type="text"
                name="social-icon-tenant_`+ inputs.length + `"
                pattern='[-\\(\\)\\=\\%\\&\\$\\;\\_\\*\\"\\#\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]{1,}'
                onchange="validateJS(event,'icon')"
                value="facebook"
                required>

              </div>

        </div>

      `)

    }

    if (type == "inputResumen") {

      $(elem).before(`
         
        <div class="row mb-3 inputResumen">

          <div class="col-md-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text bg-danger">
                  <button type="button" class="btn btn-danger btn-xs border-0" onclick="removeInput(`+ inputs.length + `,'inputResumen')"><i class="fa fa-trash"></i></button>
                </span>
              </div>

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Serie:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-serie-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Num:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-numero-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Mon:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-moneda-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Total:
                </span>
              </div>

              <input
              class="form-control" 
              type="number"
              name="item-total-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Gravada:
                </span>
              </div>

              <input
              class="form-control" 
              type="number"
              name="item-gravada-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Exonerada:
                </span>
              </div>

              <input
              class="form-control" 
              type="number"
              name="item-exonerada-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2 mt-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Inafecta:
                </span>
              </div>

              <input
              class="form-control" 
              type="number"
              name="item-inafecta-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2 mt-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Gratuita:
                </span>
              </div>

              <input
              class="form-control" 
              type="number"
              name="item-gratuita-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2 mt-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  IGV:
                </span>
              </div>

              <input
              class="form-control" 
              type="number"
              name="item-igv-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2 mt-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Tip Total:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-tipototal-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2 mt-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Cod Afect:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-codafectacion-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2 mt-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Nom Afect:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-nomafectacion-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2 mt-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Tip Afect:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-tipoafectacion-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2 mt-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Tip Cliente:
                </span>
              </div>

              <input
              class="form-control" 
              type="number"
              name="item-tipocliente-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2 mt-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Doc Cliente:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-doccliente-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

        </div>

      `)

    }

    if (type == "inputBaja") {

      $(elem).before(`
         
        <div class="row mb-3 inputBaja">

          <div class="col-md-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text bg-danger">
                  <button type="button" class="btn btn-danger btn-xs border-0" onclick="removeInput(`+ inputs.length + `,'inputBaja')"><i class="fa fa-trash"></i></button>
                </span>
              </div>

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Tip Doc:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-tipodoc-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Serie:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-serie-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-2">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Num:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-numero-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

          <div class="col-md-6">
          
            <div class="input-group">

              <div class="input-group-append">
                <span class="input-group-text group__0">
                  Motivo:
                </span>
              </div>

              <input
              class="form-control" 
              type="text"
              name="item-motivo-product_`+ inputs.length + `"
              onchange="validateJS(event,'regex')"
              required>

            </div>

          </div>

        </div>

      `)

    }

    $('[name="' + type + '"]').val(inputs.length + 1);

  } else {

    fncNotie(3, "Máximo 100 entradas permitidas");

    return;
  }

}

/*=============================================
Modo de transporte
=============================================*/
function modoT(sel) {

  if(sel.value=='') {
    $("#conductor").addClass("hidden");
    $('.tipoCond').attr("required", false);
    $('.docCond').attr("required", false);
    $('.nomComd').attr("required", false);
    $('.apeCond').attr("required", false);
    $('.licCond').attr("required", false);
  }

  if(sel.value=='01') {
    $("#conductor").addClass("hidden");
    $('.tipoCond').attr("required", false);
    $('.docCond').attr("required", false);
    $('.nomComd').attr("required", false);
    $('.apeCond').attr("required", false);
    $('.licCond').attr("required", false);
  }

  if(sel.value=='02') {
    $("#conductor").removeClass("hidden");
    $('.tipoCond').attr("required", true);
    $('.docCond').attr("required", true);
    $('.nomComd').attr("required", true);
    $('.apeCond').attr("required", true);
    $('.licCond').attr("required", true);
  }

}

/*=============================================
Cambiar entorno de la facturacion electronica
=============================================*/
function cambiaEntorno(sel) {

  if (sel.value == 'produccion') {

    $(".required").attr("required", true);
    $(".usuario-sol").val("");
    $(".clave-sol").val("");
    $(".file-certificate").val("");
    $(".clave-certificate").val("");
    $(".expired-certificate").val("");
    $(".client_id").val("");
    $(".client_secret").val("");
    $(".readonly").attr("readonly", false);
    $(".text-danger").removeClass("hidden");
    $(".usuario-sol").focus();

  } else {

    $(".required").attr("required", false);
    $(".usuario-sol").val("MODDATOS");
    $(".clave-sol").val("moddatos");
    $(".file-certificate").val("");
    $(".clave-certificate").val("");
    $(".expired-certificate").val("");
    $(".client_id").val("");
    $(".client_secret").val("");
    $(".readonly").attr("readonly", true);
    $(".text-danger").addClass("hidden");

  }

}

/*=============================================
Cambiar configuracion supabase
=============================================*/
function supabase(sel) {

  if (sel.value == 'si') {

    $("#url-supabase").attr("required", true);
    $("#url-supabase").attr("readonly", false);
    $("#key-supabase").attr("required", true);
    $("#key-supabase").attr("readonly", false);
    $("#pass-supabase").attr("required", true);
    $("#pass-supabase").attr("readonly", false);

  } else {

    $("#url-supabase").attr("required", false);
    $("#url-supabase").attr("readonly", true);
    $("#key-supabase").attr("required", false);
    $("#key-supabase").attr("readonly", true);
    $("#pass-supabase").attr("required", false);
    $("#pass-supabase").attr("readonly", true);

  }

}

/*=============================================
Funciones para meet
=============================================*/
function changedPass() {
  if ($('#meetPrv').is(":checked")) {
    $("#meetPass").removeClass("hidden");
    $("#passMeet").attr("required", true);
  } else {
    $("#meetPass").addClass("hidden");
    $("#passMeet").attr("required", false);
  }
}

function changedCal() {
  if ($('#meetCal').is(":checked")) {
    $("#calMeet").removeClass("hidden");
    $('#meetCal').val('1');
    $("#dateMeet").attr("required", true);
    $("#timeMeet").attr("required", true);
    $("#date-end").attr("required", true);
    $("#time-end").attr("required", true);
    $('#openMeet').attr('checked', false);
  } else {
    $("#calMeet").addClass("hidden");
    $('#meetCal').val('');
    $("#dateMeet").attr("required", false);
    $("#timeMeet").attr("required", false);
    $("#date-end").attr("required", false);
    $("#time-end").attr("required", false);
    $('#openMeet').attr('checked', true);
  }
}

/*=============================================
Remover entradas al formulario de productos 
=============================================*/
function removeInput(index, type) {

  var inputs = $("." + type);

  if (inputs.length > 1) {

    inputs.each(i => {

      if (i == index) {

        $(inputs[i]).remove();
      }

    })

    $('[name="' + type + '"]').val(inputs.length - 1);

  } else {

    fncNotie(3, "Debe existir al menos una entrada");

    return;

  }

}

/*=============================================
DropZone
=============================================*/
Dropzone.autoDiscover = false;
var arrayFiles = [];
var countArrayFiles = 0;

$(".dropzone").dropzone({

  url: "/",
  addRemoveLinks: true,
  acceptedFiles: "image/jpeg, image/png",
  maxFilesize: 2,
  maxFiles: 10,
  createImageThumbnails: true,
  autoProcessQueue: false,
  init: function () {

    /*=============================================
    Cuando adicionamos archivos
    =============================================*/
    this.on("addedfile", function (file) {

      //this.removeFile(file);

      countArrayFiles++;

      setTimeout(function () {

        arrayFiles.push({

          "file": file.dataURL,
          "type": file.type,
          "width": file.width,
          "height": file.height

        })

        $('[name="gallery-product"]').val(JSON.stringify(arrayFiles));

      }, 100 * countArrayFiles)

    })

    /*=============================================
    Cuando eliminamos archivos
    =============================================*/
    this.on("removedfile", function (file) {

      countArrayFiles++;

      setTimeout(function () {

        var index = arrayFiles.indexOf({

          "file": file.dataURL,
          "type": file.type,
          "width": file.width,
          "height": file.height

        })

        arrayFiles.splice(index, 1);

        $('[name="gallery-product"]').val(JSON.stringify(arrayFiles));

      }, 100 * countArrayFiles)

    })

    /*=============================================
    Obligatorio enviar archivos
    =============================================*/
    myDropzone = this;

    $(".saveBtn").click(function () {

      if (arrayFiles.length >= 1) {

        $(this).attr("type", "submit");
        myDropzone.processQueue();

      } else {

        if ($("[name='gallery-product-old']").length > 0 && $("[name='gallery-product-old']").val() != "") {

          $(this).attr("type", "submit");
          myDropzone.processQueue();

        } else {

          $(this).attr("type", "button");
          fncSweetAlert("error", "La galería no puede estar vacía.", null)

        }

      }

    })

  }

});

/*=============================================
Elegir tipo de oferta
=============================================*/
function changeOffer(type) {

  if (type.target.value == "Discount") {

    $(".typeOffer").html("Porcentaje %:");

  }

  if (type.target.value == "Fixed") {

    $(".typeOffer").html("Monto $:");

  }

}

/*=============================================
Boton regresar arriba
=============================================*/
var btnTop = $('#scroll-container');

btnTop.on('click', function (e) {
  e.preventDefault();
  $('html, body').animate({ scrollTop: 0 }, '300');
});

/*=============================================
Chat soporte
=============================================*/
$('#divChat').czmChatSupport({

  /* Button Settings */
  button: {
    position: "right",
    style: 1,
    src: '<i class="fa fa-comments"></i>',
    backgroundColor: "#25476a",
    effect: 1,
    notificationNumber: false,
    speechBubble: false,
    pulseEffect: false,
    text: {
      title: "¿Necesitas ayuda? habla con nosotros",
      description: false,
      online: "Ahora en línea",
      offline: "Volveré pronto"
    }
  },

  /* Popup Settings */
  popup: {
    automaticOpen: false,
    outsideClickClosePopup: true,
    effect: 1,
    header: {
      backgroundColor: "#25476a",
      title: "¿Necesitas ayuda? habla con nosotros",
      description: "Elije una cuenta para obtener soporte"
    },

    /* Representative Settings */
    persons: [

      /* Copy for more representatives [::Start Copy::] */
      {
        avatar: {
          src: '<i class="fa fa-whatsapp"></i>',
          backgroundColor: "#10c379",
          onlineCircle: true
        },
        text: {
          title: "Whatsapp",
          description: "Lunes a viernes de 9am - 6pm",
          online: "Ahora en línea",
          offline: "Volveré pronto"
        },
        link: {
          desktop: "https://web.whatsapp.com/send?phone=51951345257&text=Hola necesito soporte del sistema",
          mobile: "https://wa.me/51951345257/?text=Hola necesito soporte del sistema"
        },
        onlineDay: {
          sunday: false,
          monday: "00:09-18:00",
          tuesday: "00:09-18:00",
          wednesday: "00:09-18:00",
          thursday: "00:09-18:00",
          friday: "00:09-18:00",
          saturday: false
        }
      },
      /* [::End Copy::] */

      /* Copy for more representatives [::Start Copy::] */
      {
        avatar: {
          src: '<i class="fa fa-telegram"></i>',
          backgroundColor: "#18A3E6",
          onlineCircle: true
        },
        text: {
          title: "Telegram",
          description: "Lunes a viernes de 9am - 6pm",
          online: "Ahora en línea",
          offline: "Volveré pronto"
        },
        link: {
          desktop: "https://telegram.me/chanamoth",
          mobile: "https://t.me/chanamoth"
        },
        onlineDay: {
          sunday: false,
          monday: "00:09-18:00",
          tuesday: "00:09-18:00",
          wednesday: "00:09-18:00",
          thursday: "00:09-18:00",
          friday: "00:09-18:00",
          saturday: false
        }
      },
      /* [::End Copy::] */

      /* Copy for more representatives [::Start Copy::] */
      {
        avatar: {
          src: '<i class="fa fa-facebook"></i>',
          backgroundColor: "#0084ff",
          onlineCircle: true
        },
        text: {
          title: "Messenger",
          description: "Lunes a viernes de 9am - 6pm",
          online: "Ahora en línea",
          offline: "Volveré pronto"
        },
        link: {
          desktop: "http://m.me/chanamoth",
          mobile: "http://m.me/chanamoth"
        },
        onlineDay: {
          sunday: false,
          monday: "00:09-18:00",
          tuesday: "00:09-18:00",
          wednesday: "00:09-18:00",
          thursday: "00:09-18:00",
          friday: "00:09-18:00",
          saturday: false
        }
      },
      /* [::End Copy::] */

    ]
  },

  /* Other Settings */
  sound: false,
  changeBrowserTitle: false,
  cookie: false,
});

/*=============================================
Cambia el tipo de documento a buscar
=============================================*/
function cambiaDoc(sel) {

  if (sel.value == '') {

    fncNotie(5, "Por favor selecciona una opción.");
    $('#doc-long').val('0');
    $('.documento').attr('maxlength', 0);
    $('.razon-social').val('');
    $('.ubigeo').val('');
    $('.departamento').val('');
    $('.provincia').val('');
    $('.distrito').val('');
    $('.domicilio').val('');
    $('.documento').val('');
    $('.documento').attr('onchange', "sendAjaxConsult('null')");
    $('.pais').val('');
    $('.sel-documento').focus();

  }

  if (sel.value == 1) {

    $('#doc-long').val('15');
    $('.documento').attr('maxlength', 15);
    $('.razon-social').val('');
    $('.ubigeo').val('');
    $('.departamento').val('');
    $('.provincia').val('');
    $('.distrito').val('');
    $('.domicilio').val('');
    $('.documento').val('');
    $('.documento').attr('onchange', "sendAjaxConsult('null')");
    $('.pais').val('');
    $('.documento').focus();

  }

  if (sel.value == 2) {

    $('#doc-long').val('8');
    $('.documento').attr('maxlength', 8);
    $('.razon-social').val('');
    $('.ubigeo').val('');
    $('.departamento').val('');
    $('.provincia').val('');
    $('.distrito').val('');
    $('.domicilio').val('');
    $('.documento').val('');
    $('.documento').attr('onchange', "sendAjaxConsult('dni')");
    $('.pais').val('Peru');
    $('.documento').focus();

  }

  if (sel.value == 3) {

    $('#doc-long').val('12');
    $('.documento').attr('maxlength', 12);
    $('.razon-social').val('');
    $('.ubigeo').val('');
    $('.departamento').val('');
    $('.provincia').val('');
    $('.distrito').val('');
    $('.domicilio').val('');
    $('.documento').val('');
    $('.documento').attr('onchange', "sendAjaxConsult('null')");
    $('.pais').val('');
    $('.documento').focus();

  }

  if (sel.value == 4) {

    $('#doc-long').val('11');
    $('.documento').attr('maxlength', 11);
    $('.razon-social').val('');
    $('.ubigeo').val('');
    $('.departamento').val('');
    $('.provincia').val('');
    $('.distrito').val('');
    $('.domicilio').val('');
    $('.documento').val('');
    $('.documento').attr('onchange', "sendAjaxConsult('ruc')");
    $('.pais').val('Peru');
    $('.documento').focus();

  }

  if (sel.value == 5) {

    $('#doc-long').val('12');
    $('.documento').attr('maxlength', 12);
    $('.razon-social').val('');
    $('.ubigeo').val('');
    $('.departamento').val('');
    $('.provincia').val('');
    $('.distrito').val('');
    $('.domicilio').val('');
    $('.documento').val('');
    $('.documento').attr('onchange', "sendAjaxConsult('null')");
    $('.pais').val('');
    $('.documento').focus();

  }

  if (sel.value == 6) {

    $('#doc-long').val('15');
    $('.documento').attr('maxlength', 15);
    $('.razon-social').val('');
    $('.ubigeo').val('');
    $('.departamento').val('');
    $('.provincia').val('');
    $('.distrito').val('');
    $('.domicilio').val('');
    $('.documento').val('');
    $('.documento').attr('onchange', "sendAjaxConsult('null')");
    $('.pais').val('');
    $('.documento').focus();

  }

}

/*=============================================
Formatear fecha para input
=============================================*/
function formatDate(date) {
  var d = new Date(date),
    month = '' + (d.getMonth() + 1),
    day = '' + d.getDate(),
    year = d.getFullYear();

  if (month.length < 2)
    month = '0' + month;
  if (day.length < 2)
    day = '0' + day;

  return [year, day, month].join('-');
}

/*=============================================
Funcion para consultar RUC DNI por ajax
=============================================*/
function sendAjaxConsult(typeDoc) {

  var documento = $('#doc-person').val();
  var longitud = $('#doc-long').val();

  var typeComp = $('#type-comp').val();
  var rucEmisor = $('#ruc-emisor').val();
  var fechaEmision = $('#emite-comp').val();
  var serieComp = $('#serie-comp').val();
  var numberComp = $('#number-comp').val();
  var montoComp = $('#monto-comp').val();

  if (typeDoc == 'ruc' || typeDoc == 'dni') {

    if (longitud == 8 || longitud == 11) {

      if ($.trim(documento).length > 0) {

        if ($.trim(documento).length == longitud) {

          if (navigator.onLine) {

            $.ajax({

              type: 'POST',
              url: 'ajax/ajax-consulta.php',
              dataType: "json",
              data: { type: typeDoc, doc: documento },

              beforeSend: function (objeto) {

                $('#habido-ruc').html('');
                matPreloader("on");
                fncSweetAlert("loading", "Buscando...", "");

              },

              success: function (datos) {

                matPreloader("off");
                fncSweetAlert("close", "", "");

                if (typeDoc == 'ruc') {

                  if (datos.response.success == true) {

                    const dataJson = JSON.stringify(datos);

                    $('#preData').text(dataJson);

                    if (datos.response.data.estado == 'ACTIVO') {

                      var estado_color = 'success';

                    } else {

                      var estado_color = 'danger';

                    }

                    if (datos.response.data.condicion == 'HABIDO') {

                      var habido_color = 'success';

                    } else {

                      var habido_color = 'danger';

                    }

                    $('.razon-social').val(datos.response.data.razonSocial);
                    $('.nombre-comercial').val(datos.response.data.nombreComercial);
                    $('.ubigeo').val(datos.response.data.ubigeo);
                    $('.departamento').val(datos.response.data.departamento);
                    $('.provincia').val(datos.response.data.provincia);
                    $('.distrito').val(datos.response.data.distrito);
                    $('.domicilio').val(datos.response.data.direccion);
                    $('#estado-ruc').html('<small class="text-' + estado_color + '">(' + datos.response.data.estado + ')</small>');
                    $('#habido-ruc').html('<small class="text-' + habido_color + '">(' + datos.response.data.condicion + ')</small>');

                    if (datos.response.data.departamento == null && datos.response.data.provincia == null && datos.response.data.distrito == null && datos.response.data.ubigeo == null) {

                      $('.departamento').val('-');
                      $('.provincia').val('-');
                      $('.distrito').val('-');
                      $('.ubigeo').val('-');
                      $('.departamento').focus();

                    } else {

                      $('.phone-tenant').focus();

                    }

                    fncFormatInputs();
                    matPreloader("off");
                    fncSweetAlert("close", "", "");

                  } else {

                    fncFormatInputs();
                    matPreloader("off");
                    fncSweetAlert("close", "", "");
                    fncNotie(3, datos.response.message);

                  }

                } else {

                  if (datos.response.success == true) {

                    const dataJson = JSON.stringify(datos);

                    $('#preData').text(dataJson);

                    var dateNac = formatDate(datos.data.fecha_nacimiento);

                    $('.razon-social').val(datos.response.data.nombreCompleto);
                    //$('.ubigeo').val(datos.data.ubigeo[2]);
                    //$('.departamento').val(datos.data.departamento);
                    //$('.provincia').val(datos.data.provincia);
                    //$('.distrito').val(datos.data.distrito);
                    //$('.domicilio').val(datos.data.direccion);
                    //$('.fecha-nacim').val(dateNac);

                    /*if (datos.data.sexo == 'Masculino') {

                      $(".sexo").val(1);

                    } else {

                      $(".sexo").val(2);

                    }*/

                    /*if (datos.data.departamento == '-') {

                      $('.departamento').focus();

                    } else {

                      $('.email_tenant').focus();

                    }*/

                  } else {

                    fncNotie(3, datos.response.message);

                  }

                }

              }

            })

          } else {

            fncFormatInputs();
            matPreloader("off");
            fncSweetAlert("close", "", "");
            fncNotie(3, "Por favor verifica tu conexion a internet.");
            $('.documento').focus();

          }

        } else {

          fncFormatInputs();
          matPreloader("off");
          fncSweetAlert("close", "", "");
          fncNotie(5, "Por favor ingresa un documento válido de " + longitud + " dígitos.");
          $('.documento').focus();

        }

      } else {

        fncFormatInputs();
        matPreloader("off");
        fncSweetAlert("close", "", "");
        fncNotie(5, "Por favor ingresa el documento a buscar.");
        $('.documento').focus();

      }

    }

  } else {

    if (navigator.onLine) {

      $.ajax({

        type: 'POST',
        url: 'ajax/ajax-consulta.php',
        dataType: "json",
        data: { type: typeDoc, typeComp: typeComp, rucEmisor: rucEmisor, fechaEmision: fechaEmision, serieComp: serieComp, numberComp: numberComp, montoComp: montoComp },

        beforeSend: function (objeto) {

          $('#habido-ruc').html('');
          matPreloader("on");
          fncSweetAlert("loading", "Buscando...", "");

        },

        success: function (datos) {

          matPreloader("off");
          fncSweetAlert("close", "", "");

          if (datos.response.success == true) {

            const dataJson = JSON.stringify(datos);

            $('#preData').text(dataJson);

          } else {

            fncFormatInputs();
            matPreloader("off");
            fncSweetAlert("close", "", "");
            fncNotie(3, datos.response.message);

          }

        }

      })

    } else {

      fncFormatInputs();
      matPreloader("off");
      fncSweetAlert("close", "", "");
      fncNotie(3, "Por favor verifica tu conexion a internet.");
      $('.documento').focus();

    }

  }

}

/*=============================================
Enviar documento a SUNAT
=============================================*/
function sendSunat(idSale) {

  /*=============================================
  Traer información del documento
  =============================================*/
  $.ajax({

    url: 'ajax/ajax-send-invoices.php',
    data: { idSale: idSale },
    type: "GET",
    dataType: "json",

    beforeSend: function (object) {
      matPreloader("on");
      fncSweetAlert("loading", "Enviando...", "");
    },

    success: function (data) {

      if (data.response.success == true) {

        var icon = "success";
        var title = data.response.message;
        var badge = "success";

      } else if (data.response.success == false) {

        var icon = "error";
        var title = data.response.message;
        var badge = "danger";

      } else if (data.response.success == 'error') {

        var icon = "warning";
        var title = data.response.message;
        var badge = "warning";

      } else {

        var icon = "info";
        var title = 'No responde el servidor de SUNAT, por favor intenta el reenvío en unos minutos';
        var badge = "info";

      }

      fncFormatInputs();
      matPreloader("off");

      Swal.fire({
        allowOutsideClick: false,
        title: '',
        icon: icon,
        html: '<div class="alert alert-' + badge + '" role="alert" style="font-size: 14px;">' + title + '</div><a class="pointer btn btn-primary waves-effect" onclick="printSale(' + idSale + ",1" + ')">Imprimir Ticket</a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="pointer btn btn-primary waves-effect" onclick="printSale(' + idSale + ",2" + ')">Imprimir A4</a>',
        showConfirmButton: true,
        confirmButtonColor: '#df5645',
        confirmButtonText: 'Cerrar Ventana',
        showCancelButton: false,
      }).then((result) => {
        location.reload();
      });

    }

  });

}

/*=============================================
Enviar guia a SUNAT
=============================================*/
function sendSunatDesp(idDesp) {

  /*=============================================
  Traer información del documento
  =============================================*/
  $.ajax({

    url: 'ajax/ajax-send-despatches.php',
    data: { idDesp: idDesp },
    type: "GET",
    dataType: "json",

    beforeSend: function (object) {
      matPreloader("on");
      fncSweetAlert("loading", "Enviando...", "");
    },

    success: function (data) {

      fncFormatInputs();
      matPreloader("off");

      Swal.fire({
        allowOutsideClick: false,
        title: '',
        icon: 'success',
        html: '<span>' + data.response.message + '</span>',
        showConfirmButton: true,
        confirmButtonColor: '#df5645',
        confirmButtonText: 'Cerrar Ventana',
        showCancelButton: false,
      }).then((result) => {
        location.reload();
      });

    }

  });

}

/*=============================================
Enviar guia a SUNAT
=============================================*/
function sendSunatTra(idDesp) {

  /*=============================================
  Traer información del documento
  =============================================*/
  $.ajax({

    url: 'ajax/ajax-send-transport.php',
    data: { idDesp: idDesp },
    type: "GET",
    dataType: "json",

    beforeSend: function (object) {
      matPreloader("on");
      fncSweetAlert("loading", "Enviando...", "");
    },

    success: function (data) {

      fncFormatInputs();
      matPreloader("off");

      Swal.fire({
        allowOutsideClick: false,
        title: '',
        icon: 'success',
        html: '<span>' + data.response.message + '</span>',
        showConfirmButton: true,
        confirmButtonColor: '#df5645',
        confirmButtonText: 'Cerrar Ventana',
        showCancelButton: false,
      }).then((result) => {
        location.reload();
      });

    }

  });

}

/*=============================================
Enviar resumen a SUNAT
=============================================*/
function sendSunatResumen(idSum) {

  /*=============================================
  Traer información del documento
  =============================================*/
  $.ajax({

    url: 'ajax/ajax-send-resumen.php',
    data: { idSum: idSum },
    type: "GET",
    dataType: "json",

    beforeSend: function (object) {
      matPreloader("on");
      fncSweetAlert("loading", "Enviando...", "");
    },

    success: function (data) {

      fncFormatInputs();
      matPreloader("off");

      Swal.fire({
        allowOutsideClick: false,
        title: '',
        icon: 'success',
        html: '<span>' + data.response.message + '</span>',
        showConfirmButton: true,
        confirmButtonColor: '#df5645',
        confirmButtonText: 'Cerrar Ventana',
        showCancelButton: false,
      }).then((result) => {
        location.reload();
      });

    }

  });

}

/*=============================================
Enviar baja a SUNAT
=============================================*/
function sendSunatBaja(idVoided) {

  /*=============================================
  Traer información del documento
  =============================================*/
  $.ajax({

    url: 'ajax/ajax-send-baja.php',
    data: { idVoided: idVoided },
    type: "GET",
    dataType: "json",

    beforeSend: function (object) {
      matPreloader("on");
      fncSweetAlert("loading", "Enviando...", "");
    },

    success: function (data) {

      fncFormatInputs();
      matPreloader("off");

      Swal.fire({
        allowOutsideClick: false,
        title: '',
        icon: 'success',
        html: '<span>' + data.response.message + '</span>',
        showConfirmButton: true,
        confirmButtonColor: '#df5645',
        confirmButtonText: 'Cerrar Ventana',
        showCancelButton: false,
      }).then((result) => {
        location.reload();
      });

    }

  });

}

/*=============================================
Cambia el tipo de moneda en reportes
=============================================*/
function changeMoney(id) {

  $("#idMoney").val(id);

  var URLsearch = window.location.search;

  var explode = URLsearch;
  var porciones = explode.split('=');

  if (porciones[0] == "?start") {

    window.location = page + "?start=" + $("#between1").val() + "&end=" + $("#between1").val() + "&money=" + id;
    /*var URLactual = window.location.href;
    window.open(URLactual + "&money=" + id, "_top")*/

  } else {

    window.location = page + "?money=" + id;
    /*var URLactual = window.location.href;
    window.open(URLactual + "?money=" + id, "_top")*/

  }

}

/*=============================================
Autocompletar en ventas
=============================================*/
function autoCompleteSale() {

  $(function () {
    $(".search-client-sale").autocomplete({
      source: "ajax/autocomplete/client-all.php",
      minLength: 2,
      select: function (event, ui) {
        event.preventDefault();
        $('.search-client-sale').val(ui.item.name_client);
        $('.idClient').val(ui.item.id_client);
        $('.typeClient').val(ui.item.type_doc_client);
        $('.docClient').val(ui.item.document_client);
        $('.edadClient').val(ui.item.birthday_client);
        $('.sexClient').val(ui.item.sex_client);
        $('.phoneClient').val(ui.item.phone_client);
        $('.depClient').val(ui.item.departament_client);
        $('.provClient').val(ui.item.province_client);
        $('.distClient').val(ui.item.district_client);
        $('.addClient').val(ui.item.address_client);

        $(".search-client-sale").focus();
      }
    });
  });

}

/*=============================================
Cambiamos la serie del documento
=============================================*/
function changeDoc(sel) {

  if (sel.value == 1) {

    $(".serie-sale").val("F001");
    $(".idClient").val("");
    $(".typeClient").val("");
    $(".typeSearch").val("empresa");
    $(".search-client-sale").val("");
    $(".search-client-sale").focus();

  }

  if (sel.value == 3) {

    $(".serie-sale").val("B001");
    $(".idClient").val("1");
    $(".typeClient").val("2");
    $(".typeSearch").val("natural");
    $(".search-client-sale").val("Clientes Varios");

  }

  if (sel.value == 100) {

    $(".serie-sale").val("V001");
    $(".idClient").val("1");
    $(".typeClient").val("2");
    $(".typeSearch").val("all");
    $(".search-client-sale").val("Clientes Varios");

  }

}

/*=============================================
Guardar venta boleta/factura/pedido
=============================================*/
$("#saveSale").submit(function (event) {

  matPreloader("on");
  fncSweetAlert("loading", "Cargando...", "");

  var id_cliente = $(".idClient").val();
  var resibido = $(".recibed-sale").val();
  var typeSale = $(".type-sale").val();
  var typeClient = $(".typeClient").val();
  var parametros = $(this).serialize();

  if (id_cliente == "") {

    fncFormatInputs();
    matPreloader("off");
    fncSweetAlert("error", "Por favor selecciona un cliente.", "");
    $(".search-client-sale").focus();
    return false;

  }

  if (resibido == "" || resibido == 0) {

    fncFormatInputs();
    matPreloader("off");
    fncSweetAlert("error", "Por favor ingresa un valor mayor a cero.", "");
    $(".recibed-sale").focus();
    return false;
  }

  if ((typeSale == 1 && typeClient == 4) | (typeSale == 3 && typeClient == 1) | (typeSale == 3 && typeClient == 2) | (typeSale == 3 && typeClient == 3) | (typeSale == 3 && typeClient == 5) | (typeSale == 3 && typeClient == 6) | (typeSale == 100 && typeClient == 1) | (typeSale == 100 && typeClient == 2) | (typeSale == 100 && typeClient == 3) | (typeSale == 100 && typeClient == 4) | (typeSale == 100 && typeClient == 5) | (typeSale == 100 && typeClient == 6)) {

    $.ajax({
      type: "POST",
      url: "ajax/ajax-newsale.php",
      data: parametros,
      beforeSend: function (objeto) { },
      success: function (datos) {
        $("#resultSale").html(datos);
      }
    });

  } else {

    fncFormatInputs();
    matPreloader("off");
    fncSweetAlert("error", "El tipo de cliente no es válido para el tipo de documento.", "");
    $(".search-client-sale").focus();
    return false;

  }

  event.preventDefault();

})

/*=============================================
Selecciona medico admision
=============================================*/
function selectMedico() {
  var idSpecialty = $(".idSpecialty").val();
  $.ajax({
    url: "ajax/charge-medic.php",
    method: "POST",
    data: {
      "id": idSpecialty
    },
    success: function (response) {
      console.log(response);
      $(".selectMedic").show();
      $(".medicHistory").html(response);
    }
  })
}

/*=============================================
Cambiamos atributos del responsable menor edad
=============================================*/
function changeVal(sel) {

  if (sel.value == 1) {

    $(".selectRespon").hide();
    $(".respon-history").attr("required", false);
    $(".respon-history").focus();

  }

  if (sel.value == 2) {

    $(".selectRespon").show();
    $(".respon-history").attr("required", true);
    $(".respon-history").focus();

  }

}

/*=============================================
Calcula el estado del peso
=============================================*/
function Suma() {

  var peso = $('#peso').val();
  var talla = $('#talla').val();

  try {

    peso = (isNaN(parseInt(peso))) ? 0 : parseInt(peso);
    talla = (isNaN(parseInt(talla))) ? 0 : talla;

    var elevado = Math.pow(talla, 2);
    var resultado_imc = (peso / elevado).toFixed(2);

    $('#imc').val(resultado_imc)
    //
    if (resultado_imc < 18) {

      $('#estado').val('Delgadez');

    };
    if (resultado_imc > 18 && resultado_imc <= 24.9) {

      $('#estado').val('Peso adecuado');

    };
    if (resultado_imc >= 25 && resultado_imc <= 29.9) {

      $('#estado').val('Sobrepeso');

    };
    if (resultado_imc > 30) {

      $('#estado').val('Obesidad');

    };

  }

  catch (e) { }

}

/*=============================================
Ejecutar funciones globales
=============================================*/
$(function () {
  preload();
  autoCompleteSale();
});