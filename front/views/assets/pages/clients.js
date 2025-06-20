/*=============================================
Función para cargar la vista inicial
=============================================*/
function load() {

	$(document).ready(function () {

		var limit = 48;
		var start = 0;
		var action = 'inactive';
		function load_country_data(limit, start) {
			$.ajax({
				url: "ajax/data-clients.php?token=" + localStorage.getItem("token_user"),
				method: "POST",
				data: { limit: limit, start: start },
				cache: false,
				success: function (data) {
					$('#load_data').append(data);
					if (data == '') {
						$('#load_data_message').html("<span class='text-center'>No se encontraron resultados</span>");
						action = 'active';
					} else {
						$('#load_data_message').html("<div class='spinner-border text-muted my-5'></div>");
						action = "inactive";
					}
				}
			});
		}

		if (action == 'inactive') {
			action = 'active';
			load_country_data(limit, start);
		}
		$(window).scroll(function () {
			if ($(window).scrollTop() + $(window).height() > $("#load_data").height() && action == 'inactive') {
				action = 'active';
				start = start + limit;
				setTimeout(function () {
					load_country_data(limit, start);
				}, 500);
			}
		});

	});

}

/*=============================================
Ejecutar funciones globales
=============================================*/
$(function () {
	load();
});