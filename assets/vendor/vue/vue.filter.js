Vue.filter('format_number', function(value){
    return accounting.formatNumber(parseFloat(value),2) ;
});
Vue.filter('capitalize', function (value) {
    if (!value) return ''
    value = value.toString().toLowerCase();
    return value.charAt(0).toUpperCase() + value.slice(1)
});
Vue.filter('upper', function (value) {
    if (!value) return ''
    value = value.toString().toUpperCase();
    return value;
});
//convert byte to value most likely
Vue.filter('byte_to_mb', function (value) {
    if (!value) return ''

    let tamanioLegible = value;
    const unidades = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
    let indiceUnidad = 0;
    while (tamanioLegible >= 1024) {
        tamanioLegible /= 1024;
        indiceUnidad++;
    }
    tamanioLegible = tamanioLegible.toFixed(1) + " " + unidades[indiceUnidad];

    // Devolver el tamaño legible
    return tamanioLegible;

});