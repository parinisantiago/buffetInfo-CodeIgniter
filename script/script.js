function ponerFecha(){
    var aux = $('#datepicker').datepicker({ dateFormat: 'dd-mm-yy' }).val();
    aux=formatear(aux);
    document.getElementById('fechaPicker').value=aux;
}
function formatear(fecha){
    var arreglo=fecha.split("/"); 
    var result=arreglo[2].concat("-".concat(arreglo[0].concat("-".concat(arreglo[1]))));
    console.log(result);
    return (result)
    
}
