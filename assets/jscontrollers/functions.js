function show_Error(title,message) {
    $(document).Toasts('create', {
        class: 'bg-danger', 
        title: title,
        subtitle: 'Error',
        body: message,
        autohide: true,
        delay: 5000
    })
}
function text_capitalize(text) {
    let textCapitalize = text.toString();
    textCapitalize = textCapitalize.charAt(0).toUpperCase() + textCapitalize.slice(1).toLowerCase();
    return textCapitalize
}
function GetUniquedArrayObject(campo,array_seach) {
    let array_result = [];
    if (array_seach != false) {
        let key = campo;
        array_result = [...new Map(array_seach.map(item =>
            [item[key], item])).values()];
    }
    return array_result;
}
//obtener mes en curso
function getMonth() {
    let month = new Date().getMonth();
    return month;
}
//obtener año en curso
function getYear() {
    let year = new Date().getFullYear();
    return year;
}
//obtener fecha en formato dd/mm/yyyy
function getDateFormat(date) {
    let day = date.getDate();
    let month = date.getMonth() + 1;
    let year = date.getFullYear();
    return day + "/" + month + "/" + year;
}

//obtener la url base del proyecto
function getBaseURL() {
    let url = window.location.origin + "/";
    return url;
}
