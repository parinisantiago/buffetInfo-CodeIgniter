function ponerFecha(){
    var aux = $('#datepicker').datepicker({ dateFormat: 'dd-mm-yy' }).val();
    aux=formatear(aux);
    document.getElementById('fecha').value=aux;
}
function formatear(fecha){
    var arreglo=fecha.split("/"); 
    var result=arreglo[2].concat("-".concat(arreglo[0].concat("-".concat(arreglo[1]))));
    return (result)
}
function ponerFechaAM(){
    var aux = document.getElementById('datepicker').value
    aux=formatear(aux);
    document.getElementById('datepicker').value=aux;
}
function ponerFechaDoble(){
    var aux = document.getElementById('datepicker1').value
    aux=formatear(aux);
    document.getElementById('datepicker1').value=aux;
    var aux2 = document.getElementById('datepicker2').value
    aux2=formatear(aux2);
    document.getElementById('datepicker2').value=aux2;
}
