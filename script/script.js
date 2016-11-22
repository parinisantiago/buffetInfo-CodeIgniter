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