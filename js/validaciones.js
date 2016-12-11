function isValidDate(dateString, form)
{
    var error=  document.createElement("P");
    if( dateString.value.length == 0) {

        error = document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML = "Debe completar este campo";
        form.insertBefore(error, dateString);
        return true
    }
    // First check for the pattern

    fecha = /(\d{4})-(\d{2})-(\d{2})/.exec(dateString.value);
    
    if(fecha[0] != dateString.value){

        error=  document.createElement("P");
        error.setAttribute("class", "error");
        error.innerHTML ="La fecha no posee un formato valido";
        form.insertBefore(error, dateString);
        return true;
    }

    // Check the range of the day
    return false;
}


function valLetras(text, form){

    var error=  document.createElement("P");


    if( text.value.length == 0){


        error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Debe completar este campo";
        form.insertBefore(error, text);

    }else if ( /[A-Za-z]+/.exec(text.value) != text.value){

        error=  document.createElement("P");

        error.setAttribute("class", "error");
            error.innerHTML ="Solo se aceptan letras";
        form.insertBefore(error, text);
    } else {
        return(true);
    }
    return(false);
}

function valLetrasYNumeros(text, form){

    var error=  document.createElement("P");


    if( text.value.length == 0){


        error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Debe completar este campo";
        form.insertBefore(error, text);

    }else if ( /[A-Za-z09]+/.exec(text.value) != text.value){

        error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Solo se aceptan letras o numeros";
        form.insertBefore(error, text);
    } else {
        return(true);
    }
    return(false);
}

function valEmail(text, form){
    var error=  document.createElement("P");

    if( text.value.length == 0){
        error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Debe completar este campo";
        form.insertBefore(error, text);

    }else if ( /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/.exec(text.value) != text.value){
        error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Debe poseer el formato usuario@correo";
        form.insertBefore(error, text);
    } else {
        return(true);
    }
    return(false);
}

function valNumeros(text, form){
    var error=  document.createElement("P");

    if( text.value.length == 0){

        error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Debe completar este campo";
        form.insertBefore(error, text);
    }else if ( /[0-9]+/.exec(text.value) != text.value){

        error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Solo se aceptan números";
        form.insertBefore(error, text);
    } else {
        return(true);
    }
    return(false);
}

function valReales(text, form){
    var error=  document.createElement("P");

    if( text.value.length == 0){
        error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Debe completar este campo";
        form.insertBefore(error, text);
    }else if ( /[0-9,]+/.exec(text.value) != text.value){
        error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Solo se acepta el formato 00,00";
        form.insertBefore(error, text);
    } else {
        return(true);
    }
    return(false);
}

function valPalabras(text, form){
    var error=  document.createElement("P");

    if( text.value.length == 0){
        error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Debe completar este campo";
        form.insertBefore(error, text);
    }else if ( /[A-Za-z áéíóúàèìòùñ]+/.exec(text.value) != text.value){
        error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Soo se aceptan letras, numeros y espacios";
        form.insertBefore(error, text);
    } else {
        return(true);
    }
    return(false);
}

function valFechas(text, form){
    var error=  document.createElement("P");

    if( text.value.length == 0){
        error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Debe completar este campo";
        form.insertBefore(error, text);
    }else if ( /[0-9]+/.exec(text.value) != text.value){
        error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Debe poseer el formato YYYY-MM-DD";
        form.insertBefore(error, text);
    } else {
        return(true);
    }
    return(false);
}

function valUsuario() {

    document.getElementById("error").setAttribute("hidden","true");


    var bol = false;
    var form = document.getElementById("form");
    var text= document.getElementById("usuario");
    var pass = document.getElementById("clave");
    var nombre = document.getElementById("nombre");
    var apellido = document.getElementById("apellido");
    var documento = document.getElementById("documento");
    var email = document.getElementById("email");
    var telefono = document.getElementById("telefono");


    if(valLetrasYNumeros(text, form)){
        bol = true;
    }
    if(valLetrasYNumeros(pass, form))
    {
        bol = true;
    }
    if(valPalabras(nombre, form))
    {
        bol = true;
    }
    if(valPalabras(apellido, form))
    {
        bol = true;
    }
    if(valNumeros(documento, form))
    {
        bol = true;
    }
    if(valEmail(email, form))
    {
        bol = true;
    }
    if(valNumeros(telefono, form))
    {
        bol = true;
    }
    return(bol);
}

function valCompra() {


    document.getElementById("error").setAttribute("hidden","true");


    var bol = false;
    var form = document.getElementById("compra");
    var cant= document.getElementById("cantidad");
    var pass = document.getElementById("precioUnitario");



    if(valNumeros(cant, form)){
        bol = true;
    }
    if(valNumeros(pass, form))
    {
        bol = true;
    }

    return(bol);
}

function valConfig() {


    document.getElementById("error").setAttribute("hidden","true");


    var bol = false;
    var form = document.getElementById("config");
    var tit= document.getElementById("titulo");
    var email = document.getElementById("email");
    var lista = document.getElementById("lista");



    if(valPalabras(tit, form)){
        bol = true;
    }
    if(valEmail(email, form))
    {
        bol = true;
    }
    if(valNumeros(lista, form))
    {
        bol = true;
    }
    return(bol);
}

function valProducto() {
    document.getElementById("error").setAttribute("hidden","true");


    var bol = false;
    var form = document.getElementById("producto");
    var nombre = document.getElementById("nombre");
    var marca= document.getElementById("marca");
    var stock = document.getElementById("stock");
    var stockMinimo = document.getElementById("stockMinimo");
    var precioVentaUnitario = document.getElementById("precioVentaUnitario");


    if(valPalabras(nombre, form)){
        bol = true;
    }
    if(valPalabras(marca, form))
    {
        bol = true;
    }
    if(valNumeros(stock, form))
    {
        bol = true;
    }
    if(valNumeros(stockMinimo, form))
    {
        bol = true;
    }
    if(valNumeros(precioVentaUnitario, form))
    {
        bol = true;
    }

    return(bol);
}

function valMenu() {
    document.getElementById("error").setAttribute("hidden","true");

    var bol = true;
    var form = document.getElementById("menu");
    var fecha= document.getElementById("datepicker");


    if(isValidDate(fecha, form))
    {
        bol = false;
    }
    return(bol);

}

