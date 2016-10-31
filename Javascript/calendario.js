window.onload = function() { 
    ponerFechaActual();
};

var MonthName = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];
var ultimoSeleccionado = new Date().getDay();
function elemento(id){
    var elem;
    if (document.all) {
        elem = document.all(id);
    }else if (document.getElementById){
        elem = document.getElementById(id);
    }
   return elem;
}
function generarMes(MesNuevo) {
    MesActual = MesNuevo;
    generarHoja(MesActual, obtenerAnno());	
}
function generarHoja(Mes, Anno) {
    escribirMes("NombreMes", Mes);
    var Cadena = escribirTitulosColumnas();
    var Fecha = new Date(Anno, Mes, 1);
    var PrimerDiaSemana = Fecha.getDay();
    var Hoy = new Date();
    Hoy = new Date(Hoy.getFullYear(), Hoy.getMonth(), Hoy.getDate(), 0,0,0);
    var timeDia = (24 * 60 * 60 * 1000); 	
    Fecha.setTime(Fecha.getTime() - (PrimerDiaSemana * timeDia));
    do {
        var CadSemana = "<tr>";
        for (var i=0; i<7; i++) {
            if (Fecha.getMonth()!= Mes) {
                CadSemana += "<td>&nbsp;</td>";
            } else {
                if (Fecha.getTime() == Hoy.getTime()) {
                        CadSemana +="<td class='hoy diaMenu' onclick='seleccionado(this)'><a href='/Loader.php?controller=MenuController&method=menuDia&pag=0&fecha=" + Fecha.getFullYear()+"-"+Fecha.getMonth()+"-"+Fecha.getDate() + "'>" + Fecha.getDate() + "</a></b></td>";
                }else{
                    if (i == 0 ) {
                        CadSemana +="<td class='domingo diaMenu' onclick='seleccionado(this)'><b><a href='/Loader.php?controller=MenuController&method=menuDia&pag=0&fecha="  + Fecha.getFullYear()+"-"+Fecha.getMonth()+"-"+Fecha.getDate() + "'>" + Fecha.getDate() + "</a></b></td>";
                    } else {
                        CadSemana +="<td class='dia diaMenu' onclick='seleccionado(this)'><b><a href='/Loader.php?controller=MenuController&method=menuDia&pag=0&fecha=" + Fecha.getFullYear()+"-"+Fecha.getMonth()+"-"+Fecha.getDate() + "'>" + Fecha.getDate() + "</a></b></td>";
                    }
                }
            }
            Fecha.setTime(Fecha.getTime() + timeDia);
        }
        CadSemana = CadSemana + "</tr>\n";
        Cadena = Cadena + CadSemana;
        var miMes = Fecha.getMonth();
    } while (miMes == Mes);
    Cadena = Cadena + "</table>";
    //Poner el año
    var objeto = elemento("AnnoActual");
    objeto.innerHTML = Anno
    //Escribir la hoja del almanaque
    objeto = elemento("HojaMes");
    objeto.innerHTML = Cadena;
}
function ponerFechaActual() {
    Hoy = new Date();
    MesActual = Hoy.getMonth();
    Anno = Hoy.getFullYear();
    generarHoja(MesActual, Anno);
}
function escribirMes(id, mes) {
    var objeto = elemento(id);
    objeto.innerHTML = MonthName[mes];
}
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
function obtenerAnno() {
    var objeto = elemento("AnnoActual");
    return(objeto.innerHTML);
}
function annoAtras() {
   Anno--;
   generarHoja(MesActual, Anno);		
}
function annoAdelante() {
    Anno++;
    generarHoja(MesActual, Anno);		
}

/* -----*/
function seleccionado(elem){
    $(ultimoSeleccionado).attr("id" , "");
    ultimoSeleccionado = $(elem).children();
    $(ultimoSeleccionado).attr("id" , "seleccionado");
    alert($(elem).children().text());
}
