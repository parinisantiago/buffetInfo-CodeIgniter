function valLetras(text, form){

    if( text.value.length == 0){


        var error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Debe completar este campo";
        form.insertBefore(error, text);

    }else if ( /[A-Za-z]+/.exec(text.value) != text.value){

        var error=  document.createElement("P");

        error.setAttribute("class", "error");
            error.innerHTML ="Solo se aceptan letras";
        form.insertBefore(error, text);
    } else {
        return(true);
    }
    return(false);
}

function valLetrasYNumeros(text, form){

    if( text.value.length == 0){


        var error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Debe completar este campo";
        form.insertBefore(error, text);

    }else if ( /[A-Za-z09]+/.exec(text.value) != text.value){

        var error=  document.createElement("P");

        error.setAttribute("class", "error");
        error.innerHTML ="Solo se aceptan letras o numeros";
        form.insertBefore(error, text);
    } else {
        return(true);
    }
    return(false);
}

function valEmail(text){
    if( text.value.length == 0){
        text.parentNode.setAttribute("class", "form-group has-warning has-feedback");
        document.getElementById("glyphicon-nomTipo").setAttribute("class","glyphicon glyphicon-warning-sign form-control-feedback");
        document.getElementById("helpBlock-nomTipo").innerHTML = "debe completar este campo";

    }else if ( /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/.exec(text.value) != text.value){
        text.parentNode.setAttribute("class", "form-group has-error has-feedback");
        document.getElementById("glyphicon-nomTipo").setAttribute("class","glyphicon glyphicon-remove form-control-feedback");
        document.getElementById("helpBlock-nomTipo").innerHTML = "No puede poseer numeros ni simbolos";

    } else {
        return(true);
    }
    return(false);
}

function valNumeros(text){
    if( text.value.length == 0){
        text.parentNode.setAttribute("class", "form-group has-warning has-feedback");
        document.getElementById("glyphicon-nomTipo").setAttribute("class","glyphicon glyphicon-warning-sign form-control-feedback");
        document.getElementById("helpBlock-nomTipo").innerHTML = "debe completar este campo";

    }else if ( /[0-9]+/.exec(text.value) != text.value){
        text.parentNode.setAttribute("class", "form-group has-error has-feedback");
        document.getElementById("glyphicon-nomTipo").setAttribute("class","glyphicon glyphicon-remove form-control-feedback");
        document.getElementById("helpBlock-nomTipo").innerHTML = "No puede poseer letras ni simbolos";

    } else {
        return(true);
    }
    return(false);
}

function valReales(text){
    if( text.value.length == 0){
        text.parentNode.setAttribute("class", "form-group has-warning has-feedback");
        document.getElementById("glyphicon-nomTipo").setAttribute("class","glyphicon glyphicon-warning-sign form-control-feedback");
        document.getElementById("helpBlock-nomTipo").innerHTML = "debe completar este campo";

    }else if ( /[0-9,]+/.exec(text.value) != text.value){
        text.parentNode.setAttribute("class", "form-group has-error has-feedback");
        document.getElementById("glyphicon-nomTipo").setAttribute("class","glyphicon glyphicon-remove form-control-feedback");
        document.getElementById("helpBlock-nomTipo").innerHTML = "Debe segui el siguiente formato: 00,00";

    } else {
        return(true);
    }
    return(false);
}

function valPalabras(text){
    if( text.value.length == 0){
        text.parentNode.setAttribute("class", "form-group has-warning has-feedback");
        document.getElementById("glyphicon-nomTipo").setAttribute("class","glyphicon glyphicon-warning-sign form-control-feedback");
        document.getElementById("helpBlock-nomTipo").innerHTML = "debe completar este campo";

    }else if ( /[A-Za-z áéíóúàèìòùñ]+/.exec(text.value) != text.value){
        text.parentNode.setAttribute("class", "form-group has-error has-feedback");
        document.getElementById("glyphicon-nomTipo").setAttribute("class","glyphicon glyphicon-remove form-control-feedback");
        document.getElementById("helpBlock-nomTipo").innerHTML = "No puede poseer simbolos";

    } else {
        return(true);
    }
    return(false);
}

function valFechas(text){
    if( text.value.length == 0){
        text.parentNode.setAttribute("class", "form-group has-warning has-feedback");
        document.getElementById("glyphicon-nomTipo").setAttribute("class","glyphicon glyphicon-warning-sign form-control-feedback");
        document.getElementById("helpBlock-nomTipo").innerHTML = "debe completar este campo";

    }else if ( /[0-9]+/.exec(text.value) != text.value){
        text.parentNode.setAttribute("class", "form-group has-error has-feedback");
        document.getElementById("glyphicon-nomTipo").setAttribute("class","glyphicon glyphicon-remove form-control-feedback");
        document.getElementById("helpBlock-nomTipo").innerHTML = "Debe segui el siguiente formato: YYYY-MM-DD";

    } else {
        return(true);
    }
    return(false);
}

function valUsuario() {
    var bol = true;
    var form = document.getElementById("form");
    var text= document.getElementById("usuario");
    var pass = document.getElementById("clave");
    if(valLetrasYNumeros(text, form) || valLetrasYNumeros(pass, form)){

        bol = false;
    }
    return(bol);
}