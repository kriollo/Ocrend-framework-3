<?php

declare(strict_types=1);

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\models;

use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Models\Traits\DBModel;
use Ocrend\Kernel\Router\IRouter;

/**
 * Modelo Adminwys
 */
class Adminwys extends Models implements IModels {
    use DBModel;

    public function getPerfiles(string $select = '*') {
        if ($select == '*') {
            $perfiles = $this->db->query_select("SELECT nombre,url FROM tbladm_perfiles GROUP BY nombre ORDER BY nombre");
            if($perfiles != false)
                return $perfiles;
            else
                return [];
        }else{
            $perfiles = $this->db->select($select,'tbladm_perfiles',"",'Limit 1');
            return $perfiles[0];
        }
    }
    public function new_perfil(){
        try {
            global $http;

            # Obtener los datos $_POST
            $new_perfil = strtoupper($http->request->get('new_perfil'));

            # Verificar que no están vacíos
            if (Helper\Functions::e($new_perfil)) {
                throw new ModelsException('Todos los datos son necesarios');
            }
            # Registrar perfil
            $this->db->insert('tbladm_perfiles', [
                'nombre' => $new_perfil,
                'id_menu' => 0,
                'id_submenu' => 0
            ]);

            return ['success' => 1,'title' => 'Gestiona Perfil', 'message' => 'Perfil Creado con éxito.'];
        } catch (ModelsException $e) {
            return ['success' => 0,'title' => 'Gestiona Perfil', 'message' => $e->getMessage()];
        }
    }
    public function update_perfil() {
        try{
            global $http;

            # Obtener los datos $_POST
            $new_perfil = strtoupper($http->request->get('new_perfil'));
            $old_perfil = strtoupper($http->request->get('old_perfil'));


            # Verificar que no están vacíos
            if (Helper\Functions::e($new_perfil)) {
                throw new ModelsException('Todos los datos son necesarios');
            }
            # Registrar perfil
            $this->db->update('tbladm_perfiles', [
                'nombre' => $new_perfil,
            ],'nombre="'.$old_perfil.'"');

            $this->db->update('users', [
                'perfil' => $new_perfil,
            ],'perfil="'.$old_perfil.'"');

            return ['success' => 1,'title' => 'Gestiona Perfil', 'message' => 'Perfil Actualizado con éxito.'];
        } catch (ModelsException $e) {
            return ['success' => 0,'title' => 'Gestiona Perfil', 'message' => $e->getMessage()];
        }
    }
    public function get_data_perfil(){
        try {
            global $http;
            $perfil = $http->request->get('perfil');


            $query = "SELECT am.descripcion AS menu,glyphicon as icon,asm.id_menu,asm.id_submenu,asm.descripcion,asm.url, ap.url as url_inicio ,ap.nombre,if(ap.nombre IS NULL,0,1) AS checked
            FROM (tbladm_submenu as asm INNER JOIN tbladm_menu AS am ON asm.id_menu = am.id_menu)
            LEFT JOIN tbladm_perfiles as ap ON asm.id_menu=ap.id_menu AND asm.id_submenu=ap.id_submenu and ap.nombre='$perfil'
            ORDER BY asm.id_menu,asm.id_submenu";
            $result = $this->db->query_select($query);

            if($result != false){
                $data = [];
                $url_inicio = 'portal';
                foreach($result as $item){
                    if(!array_key_exists($item['menu'],$data)){
                        $data[$item['menu']] = [
                            'id_menu' => $item['id_menu'],
                            'menu' => $item['menu'],
                            'icon' => $item['icon'],
                            'submenu' => []
                        ];
                    }
                    $data[$item['menu']]['submenu'][] = [
                        'id_menu' => $item['id_menu'],
                        'id_submenu' => $item['id_submenu'],
                        'descripcion' => $item['descripcion'],
                        'checked' => $item['checked'] === '1' ? true : false,
                        'url' => $item['url']
                    ];
                    if($item['url_inicio'] != null)
                        $url_inicio = $item['url_inicio'];
                }
                return ['result_puro' => $result, 'result_formateado' => $data, 'url_inicio' => $url_inicio];
            }else
                return [];

        } catch (ModelsException $e) {
            return ['success' => 0,'title' => 'Gestiona Perfil', 'message' => $e->getMessage()];
        }
    }
    public function update_gest_perfil(){
        try {
            global $http;

            $all = $http->request->all();

            $perfil = $all['perfil'];
            $url_inicio = $all['url_inicio'];
            $data = $all['data_perfil'];

            $this->db->query("DELETE FROM tbladm_perfiles
            WHERE nombre='$perfil';");

            foreach($data as $item){
                foreach($item['submenu'] as $sub){
                    if($sub['checked'] == true){
                        $this->db->insert('tbladm_perfiles', [
                            'nombre' => $perfil,
                            'id_menu' => $item['id_menu'],
                            'id_submenu' => $sub['id_submenu'],
                            'url' => $url_inicio
                        ]);
                    }
                }
            }

            //regulariza todos los usuario con actalización de opciones
            $this->db->query("delete p from (users u inner join tbladm_perfilesuser p on u.id_user=p.id_user )
            where u.perfil='$perfil';");

            $this->db->query("Insert Into tbladm_perfilesuser(id_user,id_menu,id_submenu)
            select id_user,id_menu,id_submenu from (tbladm_perfiles p inner join users u on p.nombre=u.perfil ) where p.nombre='$perfil';");

            return ['success' => 1,'title' => 'Gestiona Perfil', 'message' => 'Perfil Actualizado con éxito.'];
        } catch (ModelsException $e) {
            return ['success' => 0,'title' => 'Gestiona Perfil', 'message' => $e->getMessage()];
        }
    }
    public function getIdMenu(string $controller, $metodo ){
        $url = $controller;
        $result = $this->db->query_select("SELECT id_menu,id_submenu FROM tbladm_submenu WHERE url LIKE '$url%' limit 1");

        if($metodo != "" AND $metodo != NULL ){
            $url.= "/".$metodo;
        }

        if($result != false){
            $result = $result[0];
            $result2 = $this->db->query_select("SELECT id_submenu FROM tbladm_submenu WHERE url='$url' limit 1");
        }else{
            $result2 = $this->db->query_select("SELECT id_menu,id_submenu FROM tbladm_submenu WHERE url='$url' limit 1");
            $result = [];
        }

        if ($result2 == false){
            $result2 = [];
        }else{
            $result2 = $result2[0];
        }


        return array_merge($result,$result2);
    }
    public function getMenuUser($id_user) {
        return $this->db->query_select("select m.id_menu,m.posi,m.seccion,m.descripcion menu,m.glyphicon,sm.id_submenu,sm.url,sm.descripcion 
        from (tbladm_perfilesuser pu inner join tbladm_menu m on pu.id_menu=m.id_menu) inner join tbladm_submenu sm on pu.id_menu=sm.id_menu and pu.id_submenu=sm.id_submenu where pu.id_user=$id_user order by m.PosI,sm.PosS");
    }
    public function getAllMenu() {
        $result = $this->db->query("SELECT m.id_menu,sm.id_submenu,m.seccion,m.descripcion menu,sm.descripcion,m.glyphicon,sm.url
        from tbladm_menu m LEFT JOIN tbladm_submenu sm ON m.id_menu=sm.id_menu  AND sm.estado=1 WHERE m.estado = 1 ORDER BY m.posi,sm.poss");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function getMenuUserByPOST(){
        $user = (New Model\Users)->getUserById($this->id_user);
        if($user['rol']==1)
            return $this->getAllMenu();
        else
            return $this->getMenuUser($this->id_user);
    }
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
        $this->startDBConexion();
    }


    // funciones globales
    public function ValidarRutCliente($rut){

        $rut = str_replace('.','',$rut);
        $rut = str_replace('-','',$rut);
        $rut = str_replace(' ','',$rut);
        $rut = str_replace('_','',$rut);
        $rut = strtoupper($rut);
        //$rut = preg_replace('/[^k0-9]/i', '', $rut);
        $dv = substr($rut, -1);
        $numero = substr($rut, 0, strlen($rut) - 1);

        if(!is_numeric($numero)){
            return false;
        }

        $result = $this->db->query_select("SELECT digitoverificador('".$numero."') as dv");
        if($result !== false){
            if($result[0]['dv'] === $dv){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }

    }
}