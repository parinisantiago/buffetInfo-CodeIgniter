function ponerFecha(){
    document.getElementById('fechaPicker').value = $( "#datepicker" ).datepicker( "getDate" );
    document.forms["fechaPickerForm"].submit();
}