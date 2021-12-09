/*=============================================
EXISTE ELEMENTO
=============================================*/
const  isInPage = node=>{

	return (node === document.body) ? false : document.body.contains(node);

}

/*=============================================
GET COOKIE
=============================================*/

const getCookie = cname=>{
	
	let name = cname + "=";
	
	let decodedCookie = decodeURIComponent(document.cookie);
	
	let ca = decodedCookie.split(';');
	
	for(let i = 0; i <ca.length; i++) {
	  let c = ca[i];
	  while (c.charAt(0) == ' ') {
		c = c.substring(1);
	  }
	
	  if (c.indexOf(name) == 0) {
		return c.substring(name.length, c.length);
	  }
	
	}
	
	return null;
  
}

/*=============================================
PRIMER LETRA MAYUSCULAS
=============================================*/

const capitalizarPrimeraLetra = (str)=>{

	return str.charAt(0).toUpperCase() + str.slice(1);

}

/*=============================================
SideBar Menu
=============================================*/

$('.sidebar-menu').tree()

/*=============================================
Data Table
=============================================*/

$(".tablas").DataTable({

	"language": {

		"sProcessing":     "Procesando...",
		"sLengthMenu":     "Mostrar _MENU_ registros",
		"sZeroRecords":    "No se encontraron resultados",
		"sEmptyTable":     "Ningún dato disponible en esta tabla",
		"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
		"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
		"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
		"sInfoPostFix":    "",
		"sSearch":         "Buscar:",
		"sUrl":            "",
		"sInfoThousands":  ",",
		"sLoadingRecords": "Cargando...",
		"oPaginate": {
		"sFirst":    "Primero",
		"sLast":     "Último",
		"sNext":     "Siguiente",
		"sPrevious": "Anterior"
		},
		"oAria": {
			"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
			"sSortDescending": ": Activar para ordenar la columna de manera descendente"
		}

	}

});

/*=============================================
 //iCheck for checkbox and radio inputs
=============================================*/

$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
  checkboxClass: 'icheckbox_minimal-blue',
  radioClass   : 'iradio_minimal-blue'
})

/*=============================================
 //input Mask
=============================================*/

//Datemask dd/mm/yyyy
$('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
//Datemask2 mm/dd/yyyy
$('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
//Money Euro
$('[data-mask]').inputmask()

/*=============================================
CORRECCIÓN BOTONERAS OCULTAS BACKEND	
=============================================*/

if(window.matchMedia("(max-width:767px)").matches){
	
	$("body").removeClass('sidebar-collapse');

}

/*=============================================
MENU ENLACE DIRECTOS A ARCHIVOS EN CARPETAS	
=============================================*/

const redireccionarMenu = (seccion)=>{

	window.location =  `index.php?ruta=${seccion}`;

}

const btnAlertas = document.getElementById('alertas');
btnAlertas.addEventListener("click",()=>{redireccionarMenu('brutur/alertas')});

const btnStatusVeterinario = document.getElementById('statusVeterinario');
btnStatusVeterinario.addEventListener("click",()=>{
	window.open(`extensiones/fpdf/generarPdf.php?accion=statusVeterinario`, '_blank');
});

const btnNotificados = document.getElementById('notificados');
btnNotificados.addEventListener("click",()=>{redireccionarMenu('brutur/notificados')});

const getQueryVariable = variable => {

	var query = window.location.search.substring(1);
  
	var vars = query.split("&");
  
	for (var i=0; i < vars.length; i++) {
  
		var pair = vars[i].split("=");
  
		if(pair[0] == variable) {
  
			return pair[1];
  
		}
  
	}
  
	return false;
  
}

const sumarFecha = (fecha,dato,cantidad) =>{

	fecha = fecha.split('-');

	let año = fecha[0];

	let mes = fecha[1];

	let dia = fecha[2];

	let nuevaFecha;

	if(dato == 'mes')	
		nuevaFecha = `${año}-${parseFloat(mes) + parseFloat(cantidad)}-${dia}`;
		
	if(dato == 'año')
		nuevaFecha = `${parseFloat(año) + parseFloat(cantidad)}-${mes}-${dia}`;

	return nuevaFecha;

}

const formatearFecha = fecha =>{

	return fecha.replace(/^(\d{4})-(\d{2})-(\d{2})$/g,'$3/$2/$1');

}

/*=============================================
ACCESOS DIRECTOS ACTUALIZAR STATUS BRUTUR Y CARGAR/MODIFICAR ACTA AFTOSA
=============================================*/

window.addEventListener("keydown", function (event) {
	
	let functionKey = event.key;
	
	if(functionKey == 'F8'){
		
		$('#ventanaModalModificarStatus').modal('toggle');
	
	}
	
	
	if(functionKey == 'F9'){
	
		let campania = getCookie('campania');
	
		comprobarCampania(campania);
		
	}
       
 
	},false);

/*=============================================
COMPROBAR CAMPANIA
=============================================*/	

$('#menuAftosa').on('click',()=>{

	let campania = getCookie('campania');

	setTimeout(()=>{

		if(campania == null)
			document.getElementById('desplegableAftosa').style.display = 'none'
	
		},500)
	
	
	validarCampania()
});


/*=============================================
ASIGNAR CAMPAÑA
=============================================*/	

$('#asignarCampania').click(()=>{

	let campania = $('#campaniaNum').val();

	document.cookie = `campania=${campania}`

	$('#ventanaModalCampania').modal('toggle');


});

/*=============================================
VALIDAR CAMPAÑA
=============================================*/	

const validarCampania = ()=>{

	let campania = getCookie('campania');
	
	if(campania == null)  
		$('#ventanaModalCampania').modal('toggle');

}