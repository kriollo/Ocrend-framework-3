
'use strict';
const { createApp } = Vue;

const app = createApp({});

if(mode_build == "dev"){
    app.config.devtools = true;
    app.config.performance = true;
    app.config.warnHandler = function(msg, vm, trace) {
        console.warn(msg, vm, trace);
    };
    app.config.errorHandler = function(err, vm, info) {
        console.error(err, vm, info);
    };
}else{
    app.config.devtools = false;
    app.config.performance = true;
}
