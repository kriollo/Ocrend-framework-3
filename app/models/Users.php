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
use Ocrend\Kernel\Router\IRouter;

use Ocrend\Kernel\Models\Traits\RedBeanModel;
use RedBeanPHP\R;

/**
 * Modelo Prueba para conexión con RedBeanPHP
 */
class Users extends Models implements IModels {
    use RedBeanModel;

    /**
     * Constructor de la clase y conexión con RedBeanPHP
     *
     * @param IRouter $router: Objeto de enrutamiento
     *
     * @return void
     */
    public function __construct(IRouter $router = null)
    {
        RedBeanModel::startRedBeanConexion();

        parent::__construct($router);
    }

    /**
     * Destructor de la clase y cierre de conexión con RedBeanPHP
     *
     * @return void
     */
    public function __destruct()
    {
        RedBeanModel::closeRedBeanConexion();
    }

     /**
     * Máximos intentos de inincio de sesión de un usuario
     *
     * @var int
     */
    const MAX_ATTEMPTS = 5;

    /**
     * Tiempo entre máximos intentos en segundos
     *
     * @var int
     */
    const MAX_ATTEMPTS_TIME = 120; // (dos minutos)

    /**
     * Log de intentos recientes con la forma 'email' => (int) intentos
     *
     * @var array
     */
    private $recentAttempts = [];

    /**
     * Hace un set() a la sesión login_user_recentAttempts con el valor actualizado.
     *
     * @return void
    */
    private function updateSessionAttempts() {
        global $session;

        $session->set('login_user_recentAttempts', $this->recentAttempts);
    }

    /**
     * Revisa si las contraseñas son iguales
     *
     * @param string $pass : Contraseña sin encriptar
     * @param string $pass_repeat : Contraseña repetida sin encriptar
     *
     * @throws ModelsException cuando las contraseñas no coinciden
     */
    private function checkPassMatch(string $pass, string $pass_repeat) {
        if ($pass != $pass_repeat) {
            throw new ModelsException('Las contraseñas no coinciden.');
        }
    }

    /**
     * Verifica el email introducido, tanto el formato como su existencia en el sistema
     *
     * @param string $email: Email del usuario
     *
     * @throws ModelsException en caso de que no tenga formato válido o ya exista
     */
    public function checkEmail(string $email) {
        // Formato de email
        if (!Helper\Strings::is_email($email)) {
            throw new ModelsException('El email no tiene un formato válido.');
        }
        // Existencia de email
        $query = R::findOne('users', 'email = ?', [$email]);
        if (null !== $query) {
            throw new ModelsException('El email introducido ya existe.');
        }
    }

    /**
     * Restaura los intentos de un usuario al iniciar sesión
     *
     * @param string $email: Email del usuario a restaurar
     *
     * @throws ModelsException cuando hay un error de lógica utilizando este método
     * @return void
     */
    private function restoreAttempts(string $email) {
        if (array_key_exists($email, $this->recentAttempts)) {
            $this->recentAttempts[$email]['attempts'] = 0;
            $this->recentAttempts[$email]['time'] = null;
            $this->updateSessionAttempts();
        } else {
            throw new ModelsException('Error lógico');
        }
    }

    /**
     * Genera la sesión con el id del usuario que ha iniciado
     *
     * @param array $user_data: Arreglo con información de la base de datos, del usuario
     *
     * @return void
     */
    private function generateSession(array $user_data) {
        global $session, $cookie, $config;

        // Generar un session hash
        $cookie->set('session_hash', md5((string)time()), $config['sessions']['user_cookie']['lifetime']);

        // Generar la sesión del usuario
        $session->set($cookie->get('session_hash'). $config['sessions']['unique'] . '__user_id',(int) $user_data['id_user']);

        // Generar data encriptada para prolongar la sesión
        if($config['sessions']['user_cookie']['enable']) {
            // Generar id encriptado
            $encrypt = Helper\Strings::ocrend_encode($user_data['id_user'], $config['sessions']['user_cookie']['key_encrypt']);

            // Generar cookies para prolongar la vida de la sesión
            $cookie->set('appsalt', Helper\Strings::hash($encrypt), $config['sessions']['user_cookie']['lifetime']);
            $cookie->set('appencrypt', $encrypt, $config['sessions']['user_cookie']['lifetime']);
        }
    }

    /**
     * Verifica en la base de datos, el email y contraseña ingresados por el usuario
     *
     * @param string $email: Email del usuario que intenta el login
     * @param string $pass: Contraseña sin encriptar del usuario que intenta el login
     *
     * @return bool true: Cuando el inicio de sesión es correcto 
     *              false: Cuando el inicio de sesión no es correcto
     */
    private function authentication(string $email,string $pass) : bool {

        $query = R::findOne('users','email = ?',[ $email ]);
        // Incio de sesión con éxito
        if(null !== $query && Helper\Strings::chash($query->pass,$pass) && $query->estado == 1) {

            // Restaurar intentos
            $this->restoreAttempts($email);

            // Generar la sesión
            $this->generateSession($query->export());
            return true;
        }

        return false;
    }

    /**
     * Establece los intentos recientes desde la variable de sesión acumulativa
     *
     * @return void
     */
    private function setDefaultAttempts() {
        global $session;

        if (null != $session->get('login_user_recentAttempts')) {
            $this->recentAttempts = $session->get('login_user_recentAttempts');
        }
    }

    /**
     * Establece el intento del usuario actual o incrementa su cantidad si ya existe
     *
     * @param string $email: Email del usuario
     *
     * @return void
     */
    private function setNewAttempt(string $email) {
        if (!array_key_exists($email, $this->recentAttempts)) {
            $this->recentAttempts[$email] = [
                'attempts' => 0, // Intentos
                'time' => null // Tiempo 
            ];
        } 

        $this->recentAttempts[$email]['attempts']++;
        $this->updateSessionAttempts();
    }

    /**
     * Controla la cantidad de intentos permitidos máximos por usuario, si llega al límite,
     * el usuario podrá seguir intentando en self::MAX_ATTEMPTS_TIME segundos.
     *
     * @param string $email: Email del usuario
     *
     * @throws ModelsException cuando ya ha excedido self::MAX_ATTEMPTS
     * @return void
     */
    private function maximumAttempts(string $email) {
        if ($this->recentAttempts[$email]['attempts'] >= self::MAX_ATTEMPTS) {

            // Colocar timestamp para recuperar más adelante la posibilidad de acceso
            if (null == $this->recentAttempts[$email]['time']) {
                $this->recentAttempts[$email]['time'] = time() + self::MAX_ATTEMPTS_TIME;
            }

            if (time() < $this->recentAttempts[$email]['time']) {
                // Setear sesión
                $this->updateSessionAttempts();
                // Lanzar excepción
                throw new ModelsException('Ya ha superado el límite de intentos para iniciar sesión.');
            } else {
                $this->restoreAttempts($email);
            }
        }
    }

    /**
     * Obtiene datos de un usuario según su id en la base de datos
     *
     * @param int : Id del usuario a obtener
     * @param string $select : Por defecto es *, se usa para obtener sólo los parámetros necesarios 
     *
     * @return false|array con información del usuario
     */
    public function getUserById(int $id , string $select = '*') {
        return R::getRow('SELECT ' . $select . ' FROM users WHERE id_user = ? ',[ $id ]);
    }

    /**
     * Obtiene datos de un usuario según su id en la base de datos por medio de una llamada POST
     *
     * @return false|array con información del usuario
     */
    public function getUserByIdPOST() {
        global $http;
        $id_user = (int)$http->request->get('id_user');
        $result = $this->getUserById($id_user);
        return $result === null ? false : $result;
    }

    /**
     * Obtiene a todos los usuarios
     *
     * @param string $select : Por defecto es *, se usa para obtener sólo los parámetros necesarios 
     *
     * @return false|array con información de los usuarios
     */
    public function getUsers(string $select = '*',string $filtro = '1=1') {
        Return R::getAll('SELECT ' . $select . ' FROM users WHERE ' . $filtro);
        //return R::findAll('users',$filtro);
    }

    /**
     * Obtiene datos del usuario conectado actualmente
     *
     * @param string $select : Por defecto es *, se usa para obtener sólo los parámetros necesarios
     *
     * @throws ModelsException si el usuario no está logeado
     * @return array con datos del usuario conectado
     */
    public function getOwnerUser(string $select = '*') : array {
        if(null !== $this->id_user) {

            $user = $this->getUserById($this->id_user,$select);

            // Si se borra al usuario desde la base de datos y sigue con la sesión activa
            if(null === $user) {
                $this->logout();
            }

            return $user;
        }

        throw new \RuntimeException('El usuario no está logeado.');
    }

     /**
     * Realiza la acción de login dentro del sistema
     *
     * @return array : Con información de éxito/falla al inicio de sesión.
     */
    public function login() : array {
        try {
            global $http;

            // Definir de nuevo el control de intentos
            $this->setDefaultAttempts();   

            // Obtener los datos $_POST
            $email = strtolower($http->request->get('email'));
            $pass = $http->request->get('pass');

            // Verificar que no están vacíos
            if (Helper\Functions::e($email, $pass)) {
                throw new ModelsException('Credenciales incompletas.');
            }

            // Añadir intentos
            $this->setNewAttempt($email);

            // Verificar intentos 
            $this->maximumAttempts($email);

            // Autentificar
            if ($this->authentication($email, $pass)) {
                return ['success' => 1, 'message' => 'Conectado con éxito.'];
            }
            
            throw new ModelsException('Credenciales incorrectas.');

        } catch (ModelsException $e) {
            return ['success' => 0, 'message' => $e->getMessage()];
        }        
    }

    /**
     * Realiza la acción de registro dentro del sistema
     *
     * @return array : Con información de éxito/falla al registrar el usuario nuevo.
     */
    public function registeruser() : array {
        try {
            global $http;
            // Obtener los datos $_POST
            $data = $http->request->get('user_data');

            $name = (string)$data['name'];
            $email = mb_strtolower((string)$data['email']);
            $pass = (string)$data['pass'];
            $pass_repeat = (string)$data['pass_repeat'];
            $perfil = (string)$data['perfil'];
            $pagina_inicio = (string)$data['pagina_inicio'];
            $rol = (int)$data['rol'] === 'true' ? 1 : 0;

            // Verificar que no están vacíos
            if (Helper\Functions::e($name, $email, $pass, $pass_repeat)) {
                throw new ModelsException('Todos los datos son necesarios');
            }elseif ($perfil == '--'){
                throw new ModelsException('Debe seleccionar un perfil');
            }

            // Verificar email
            $this->checkEmail($email);

            // Veriricar contraseñas
            $this->checkPassMatch($pass, $pass_repeat);

            // Registrar al usuario
            $user = R::dispense('users');
            $user->name = $name;
            $user->email = $email;
            $user->pass = Helper\Strings::hash($pass);
            $user->perfil = $perfil;
            $user->pagina_inicio = $pagina_inicio;
            $user->rol = $rol;
            $user->fecha_pass = date('Y-m-d');
            $id_user = R::store($user);

            // duplica id sólo para RedBeanPHP
            $user = R::load('users', $id_user);
            $user->id_user = $id_user;
            R::store($user);


            // Asigna menu a usuario
            if ('DEFINIDO' != $perfil ){
                R::exec("DELETE from tbladmperfilesuser WHERE id_user=?;",[$id_user]);

                R::exec("Insert Into tbladmperfilesuser(id_user,id_menu,id_submenu)
                select ?,id_menu,id_submenu from tbladmperfiles where nombre=?;",[$id_user,$perfil]);
            }

            return ['success' => 1, 'title' => 'Maestro de Usuario','message' => 'Registrado con éxito.'];
        } catch (ModelsException $e) {
            return ['success' => 0,'title' => 'Maestro de Usuario', 'message' => $e->getMessage()];
        }
    }
    public function updateuser() : array {
        try {
            global $http;

            $data = $http->request->get('user_data');

            $id_user = (int)$data['id_user'];
            $name =  (string)$data['name'];
            $email = strtolower((string)$data['email']);
            $perfil =  (string)$data['perfil'];
            $pagina_inicio =  (string)$data['pagina_inicio'];
            $rol =  (int)($data['rol'] === 'true' ? 1 : 0);

            // Verificar que no están vacíos
            if (Helper\Functions::e($name, $email)) {
                throw new ModelsException('Todos los datos son necesarios');
            }elseif ($perfil == '--'){
                throw new ModelsException('Debe seleccionar un perfil');
            }

            $user = R::load('users', $id_user);
                $user->name = $name;
                $user->email = $email;
                $user->perfil = $perfil;
                $user->pagina_inicio = $pagina_inicio;
                $user->rol = $rol;
                $user->fecha_pass = date('Y-m-d');
            R::store($user);

            // Asigna menu a usuario
            if ('DEFINIDO' != $perfil ){
                R::exec("DELETE from tbladmperfilesuser WHERE id_user=?;",[$id_user]);

                R::exec("Insert Into tbladmperfilesuser(id_user,id_menu,id_submenu)
                select ?,id_menu,id_submenu from tbladmperfiles where nombre=?;",[$id_user,$perfil]);
            }

            return ['success' => 1, 'title' => 'Maestro de Usuario','message' => 'Actualizado con éxito.'];
        } catch (ModelsException $e) {
            return ['success' => 0,'title' => 'Maestro de Usuario', 'message' => $e->getMessage()];
        }
    }
    /**
      * Envía un correo electrónico al usuario que quiere recuperar la contraseña, con un token y una nueva contraseña.
      * Si el usuario no visita el enlace, el sistema no cambiará la contraseña.
      *
      * @return array<string,integer|string>
    */
    public function lostpass() : array {
        try {
            global $http, $config;

            // Obtener datos $_POST
            $email = $http->request->get('email');

            // Campo lleno
            if (Helper\Functions::emp($email)) {
                throw new ModelsException('El campo email debe estar lleno.');
            }

            // Verificar email
            if (!Helper\Strings::is_email($email)) {
                throw new ModelsException('El email no tiene un formato válido.');
            }
            // Obtener información del usuario
            $user_data = R::findOne('users', 'email = ?', [$email])->export();

            // Verificar correo en base de datos
            if (null === $user_data) {
                throw new ModelsException('El email no está registrado en el sistema.');
            }

            // Generar token y contraseña
            $token = md5((string)time());
            $pass = uniqid();
            $link = $config['build']['url'] . 'lostpass?token='.$token.'&user='.$user_data['id_user'];

            // Construir mensaje y enviar mensaje
            $HTML = 'Hola <b>'. $user_data['name'] .'</b>, ha solicitado recuperar su contraseña perdida, si no ha realizado esta acción no necesita hacer nada.
					<br />
					<br />
					Para cambiar su contraseña por <b>'. $pass .'</b> haga <a href="'. $link .'" target="_blank">clic aquí</a> o en el botón de recuperar.';


            // Enviar el correo electrónico
            $dest = [];
			$dest[$email] = $user_data['name'];

            $email_send = Helper\Emails::send($dest,array(
                '{{title}}' => 'Recuperar contraseña de ' . $config['build']['name'],
                '{{url_logo}}' => $config['build']['url'],
                '{{logo}}' => ___ROOT___ .$config['mailer']['logo'],
                '{{content}}' => $HTML,
                '{{btn-href}}' => $link,
                '{{btn-name}}' => 'Recuperar Contraseña',
                '{{copyright}}' => '&copy; '.date('Y') .' <a href="'.$config['build']['url'].'">'.$config['build']['name'].'</a> - Todos los derechos reservados.'
              ),0);

            // Verificar si hubo algún problema con el envío del correo
            if(false === $email_send) {
                throw new ModelsException('No se ha podido enviar el correo electrónico.');
            }

            // Actualizar datos
            $user = R::load('users', $user_data['id_user']);
                $user->tmp_pass = Helper\Strings::hash($pass);
                $user->token = $token;
            R::store($user);

            return ['success' => 1, 'message' => 'Se ha enviado un enlace a su correo electrónico.'];
        } catch(ModelsException $e) {
            return ['success' => 0, 'message' => $e->getMessage()];
        }
    }

    /**
     * Desconecta a un usuario si éste está conectado, y lo devuelve al inicio
     *
     * @return void
     */
    public function logout() {
        global $session, $cookie, $config;

        $this->update_online_user("out");

        $session->remove($cookie->get('session_hash'). $config['sessions']['unique'] . '__user_id');
        foreach($cookie->all() as $name => $value) {
            $cookie->remove($name);
        }

        Helper\Functions::redir();
    }

    /**
     * Cambia la contraseña de un usuario en el sistema, luego de que éste haya solicitado cambiarla.
     * Luego retorna al sitio de inicio con la variable GET success=(bool)
     *
     * La URL debe tener la forma URL/lostpass?token=TOKEN&user=ID
     *
     * @return void
     */
    public function changeTemporalPass() {
        global $config, $http;

        // Obtener los datos $_GET
        $id_user = $http->query->get('user');
        $token = $http->query->get('token');

        $success = false;
        if (!Helper\Functions::emp($token) && is_numeric($id_user) && $id_user >= 1) {

            $user = R::load('users', $id_user);
            if($user->token === $token) {
                    $user->pass = $user->tmp_pass;
                    $user->tmp_pass = null;
                    $user->token = null;
                R::store($user);

                // Éxito
                $success = true;
            }
        }

        // Devolover al sitio de inicio
        Helper\Functions::redir($config['build']['url'] . '?sucess=' . (int) $success);
    }
    public function update_estado_user() {
        global $config;

        // Actualiza Estado
        $user = R::load('users', $this->id);
            $user->estado = !$user->estado;
        R::store($user);

        // Redireccionar a la página principal del controlador
        Helper\Functions::redir($config['build']['url'] . 'users/usuarios');
    }
    
    public function updateAvatar(){
        try {
            global $http, $config;

            $id_user = $http->request->get('id_user');
            $avatar = $http->files->get('avatar');

            if (null != $avatar && true == Helper\Files::is_image($avatar->getClientOriginalName()) ){
                $ext_foto = $avatar->getClientOriginalExtension();
                $img_name = $id_user.'.'.$ext_foto;

                $avatar->move(___ROOT___ . $config['router']['avatar'], $img_name);
                R::exec("UPDATE users SET foto=1, name_foto=? WHERE id_user=?", [$img_name, $id_user]);

                return ['success' => 1, 'message' => "Avatar Actualizado..."];
            }

            return ['success' => 0, 'message' => "No es una imagen valida..."];

        } catch (ModelsException $e) {
            return ['success' => 0, 'message' => $e->getMessage()];
        } 
    }
    public function resetpass() : array {
        try {
            global $http;

            // Obtener los datos $_POST
            $id_user = $http->request->get('id_user');
            $pass = $http->request->get('pass_new');
            $pass_repeat = $http->request->get('repass_new');

            // Verificar que no están vacíos
            if (Helper\Functions::e($pass, $pass_repeat)) {
                throw new ModelsException('Todos los datos son necesarios');
            }

            // Veriricar contraseñas
            $this->checkPassMatch($pass, $pass_repeat);

            $pass = Helper\Strings::hash($pass);

            // Actualiza contraseña
            $fecha_reset = date ( 'Y-m-j',strtotime ( '+60 day' , strtotime ( date("Y-m-d") ) ));

            R::exec("UPDATE users SET pass=?, fecha_pass=?, tmp_pass='', token=''
            WHERE id_user=? LIMIT 1;", [$pass, $fecha_reset,$id_user]);

            return ['success' => 1, 'title' => 'Reset Password Usuario','message' => 'Password actualizada con éxito.'];
        } catch (ModelsException $e) {
            return ['success' => 0, 'title' => 'Reset Password Usuario', 'message' => $e->getMessage()];
        }
    }

    /**
     * Obtiene el menu asignado al usuario, pero si su rol es administrador, obtiene todos los menus
     * @return array
     */
    public function getMenuOwnerUser(int $rol = 0) {
        return $rol == 1 ? (new Model\Adminwys)->getAllMenu(): (new Model\Adminwys)->getMenuUser($this->id_user);
    }

    /**
     * Actualiza el estado de conexión del usuario, seteando la fecha de conexión
     * @return void
     */
    public function update_online_user($opcion) {
        $ahora = time();
        $limite = $ahora-24*60;
        R::exec("UPDATE users SET online_fecha=0 WHERE online_fecha < ?;", [$limite]);

        if ($opcion === 'in')
            R::exec("UPDATE users SET online_fecha = $ahora WHERE id_user = ? LIMIT 1;", [$this->id_user]);

         if ($opcion === 'out')
            R::exec("UPDATE users SET online_fecha=0 WHERE id_user = ? LIMIT 1;", [$this->id_user]);
    }

    /**
     * Obtiene la fecha de cambio de contraseña
     * @return bool
     */
    public function validar_cambio_pass($fechapass)
    {

        $dias = Helper\Functions::getDiffDays(date("Y-m-d"),$fechapass);
        if( (int)$dias<= 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Obtiene las opciones de menu del usuario
     * @return array
     */
    public function getOpcionesMenu()
    {
        global $http;
        $id_user = $http->request->get('id_user');

        $sql = "SELECT am.descripcion AS menu,glyphicon as icon,asm.id_menu,asm.id_submenu,asm.descripcion,asm.url, if(pu.id_menu IS NULL,0,1) AS checked
        FROM (tbladmsubmenu as asm INNER JOIN tbladmmenu AS am ON asm.id_menu = am.id_menu)
        LEFT JOIN tbladmperfilesuser as pu ON asm.id_menu=pu.id_menu AND asm.id_submenu=pu.id_submenu and pu.id_user = {$id_user}
        ORDER BY asm.id_menu,asm.id_submenu";

        $result = R::getAll($sql);

        if($result != false){
            $data = [];
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
            }
            return ['result_puro' => $result, 'result_formateado' => $data,];
        }else
            return [];
    }

    /**
     * Actualiza las opciones del perfil del usuario
     * @return array
     */
    public function updatePerfilUsuario() 
    {
        try {
            global $http;

            $all = $http->request->all();
            $data = $all['perfil'];
            $id_user = $all['id_user'];

            R::exec("DELETE FROM tbladmperfilesuser WHERE id_user=?;", [$id_user]);

            foreach($data as $item){
                foreach($item['submenu'] as $sub){
                    if($sub['checked'] == true){
                        $perfil = R::dispense('tbladmperfilesuser');
                            $perfil->id_user =(int)$id_user;
                            $perfil->id_menu = (int)$item['id_menu'];
                            $perfil->id_submenu = (int)$sub['id_submenu'];
                        R::store($perfil);
                    }
                }
            }

            $user = R::load('users', $id_user);
                $user->perfil = 'DEFINIDO';
            R::store($user);

            return ['success' => 1, 'message' => 'Registrado con éxito.'];
        } catch (ModelsException $e) {
            return ['success' => 0, 'message' => $e->getMessage()];
        }
    }
}