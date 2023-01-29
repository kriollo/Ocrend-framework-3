<?php

declare(strict_types=1);

/**
 * This file is part of the Ocrend Framewok 3 package.
 *
 * API Controllers para gestión de perfiles
 *
 */


use app\models as Model;
$app->post('/getPerfiles', function() use($app) {
    $u = new Model\Adminwys();
    return $app->json($u->getPerfiles());
});
$app->post('/new_perfil', function() use($app) {
    $u = new Model\Adminwys();
    return $app->json($u->new_perfil());
});
$app->post('/update_perfil', function() use($app) {
    $u = new Model\Adminwys();
    return $app->json($u->update_perfil());
});
$app->post('/get_data_perfil', function() use($app) {
    $u = new Model\Adminwys();
    return $app->json($u->get_data_perfil());
});
$app->post('/update_gest_perfil', function() use($app) {
    $u = new Model\Adminwys();
    return $app->json($u->update_gest_perfil());
});

$app->post('/get_menu_user_by_POST', function() use($app) {
    $u = new Model\Adminwys();
    return $app->json($u->getMenuUserByPOST());
});

