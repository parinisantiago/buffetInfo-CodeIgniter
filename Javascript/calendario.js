//Nombres de meses
var MonthName = ['Enero', 
                'Febrero', 
		'Marzo', 
		'Abril', 
		'Mayo', 
		'Junio', 
		'Julio', 
		'Agosto', 
		'Setiembre', 
		'Octubre', 
		'Noviembre', 
		'Diciembre'];

//Funcion que devuelve una referencia a un elemento
function elemento(id){
 var elem;
 if (document.all) elem = document.all(id);
 else if (document.getElementById) elem = document.getElementById(id);
 return elem;
}

//Funcion que subraya el menu sobre el que se encuentra el cursor
function subrayar(id) {
var objeto = elemento(id)
     objeto.style.textDecoration = "underline";
}

function nosubrayar(id) {
var objeto = elemento(id)
    objeto.style.textDecoration = "none"
}

//Funci�n para setear la fecha actual
function ponerFechaActual() {
    //alert('Estoy poniendo la fecha');
		Hoy = new Date();
		MesActual = Hoy.getMonth();
		Anno = Hoy.getFullYear();
		generarHoja(MesActual, Anno);
}

//Funci�n que escribe el nombre del mes seleccionado
function escribirMes(id, mes) {
    var objeto = elemento(id);
    objeto.innerHTML = MonthName[mes];   
}

//Funci�n que escribe los nombres de los d�as
function escribirTitulosColumnas() {
    var cadena;
    cadena = "<table width='100%'>";
    cadena = cadena + "<tr>"
    + "<th class='hoja'>Dom</th>" 
    + "<th class='hoja'>Lun</th>" 
    + "<th class='hoja'>Mar</th>" 
    + "<th class='hoja'>Mie</th>" 
    + "<th class='hoja'>Jue</th>" 
    + "<th class='hoja'>Vie</th>" 
    + "<th class='hoja'>Sab</th>" +
    "</tr>";
    return cadena;
}

//Funci�n que genera la hoja del almanaque
function generarHoja(Mes, Anno) {
    var Fecha, PrimerDiaSemana, Hoy;										//Fechas
    var miMes, i, miDia, timeDia;												//Enteros
    var Cadena, CadSemana;															//Cadenas
    var objeto;																					//Un elemento HTML
    escribirMes("NombreMes", Mes);
    Cadena = escribirTitulosColumnas(); 
    Fecha = new Date(Anno, Mes, 1);
    PrimerDiaSemana = Fecha.getDay();
    Hoy = new Date();
    Hoy = new Date(Hoy.getFullYear(), Hoy.getMonth(), Hoy.getDate(), 0,0,0);
    timeDia = (24 * 60 * 60 * 1000); 								//Milisegundos de un d�a
    Fecha.setTime(Fecha.getTime() - (PrimerDiaSemana * timeDia));
    do {
        CadSemana = "<tr>";
        for (i=0; i<7; i++) {
            if (Fecha.getMonth()!= Mes) {
                CadSemana=CadSemana + "<td>&nbsp;</td>";
            } else {
		if (i == 0) {
                    if (Fecha.getTime() == Hoy.getTime()) {
                        CadSemana = CadSemana + 
                    "<td class='hoy'><b>" + Fecha.getDate() + 
                    "</b></td>\n";
                    } else {
			CadSemana = CadSemana + 
			"<td class='domingo'><b>" + 
			Fecha.getDate() + "</b></td>";
                    }	//Fin de if (Fecha.getTime() == Hoy.getTime()) 
                } else {
                    if (Fecha.getTime() == Hoy.getTime()) {
                        CadSemana = CadSemana + 
			"<td class='hoy'><b>" +
			Fecha.getDate() + "</b></td>";
                    } else {
			CadSemana = CadSemana + 
			"<td class='dia'><b>" + 
			Fecha.getDate() + "</b></td>";
                    }	//Fin de if (Fecha.getTime()==Hoy.getTime())
                }	//Fin de if (i == 0)
            }	//Fin de (Fecha.getMonth()!= Mes)
            Fecha.setTime(Fecha.getTime() + timeDia);
        }//Fin del bloque for
        CadSemana = CadSemana + "</tr>\n";
	Cadena = Cadena + CadSemana;
        miMes = Fecha.getMonth();
    } while (miMes == Mes);
    Cadena = Cadena + "</table>";
    //Poner el a�o
    objeto = elemento("AnnoActual");
    objeto.innerHTML = Anno
    //Escribir la hoja del almanaque
    objeto = elemento("HojaMes");
    objeto.innerHTML = Cadena;
}

function generarMes(MesNuevo) {
    MesActual = MesNuevo;
    generarHoja(MesActual, obtenerAnno());	
}

function annoAtras() {
   Anno--;
   generarHoja(MesActual, Anno);		
}

function annoAdelante() {
    Anno++;
    generarHoja(MesActual, Anno);		
}

function obtenerAnno() {
    var objeto = elemento("AnnoActual");
    return(objeto.innerHTML);
}