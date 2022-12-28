<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Helpers;

use Swift_Image;

/**
 * Helper con funciones útiles para trabajar con evío de correos mediante mailer.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */

class Emails {
    
    /**
     * Ruta en la qeu están guardados los templates. 
     * 
     * @var string
     */
    const TEMPLATES_ROUTE = ___ROOT___ . 'assets/mail/';

    /**
     * Lista de plantillas.
     * 
     * @var array
     */
    const TEMPLATES = [
        'tpl-btn.html',
        'tpl-no-btn.html'
    ];

    /**
     * Carga una plantilla y sistituye su contenido.
     * 
     * @param array $content: Contenido de cada elemento
     * @param int $template: Plantilla seleccionada
     * 
     * @return string plantilla llena
     */
    public static function loadTemplate(array $content, int $template) : string {
        # Verificar que existe la plantilla
        if(!array_key_exists($template,self::TEMPLATES)) {
            throw new \RuntimeException('La plantilla seleccionada no se encuentra.');
        }

        # Cargar contenido
        $tpl = Files::read_file(self::TEMPLATES_ROUTE . self::TEMPLATES[$template]);

        # Reempalzar contenido
        foreach($content as $index => $html) {
            $tpl = str_replace($index,$html,$tpl);
        }

        return $tpl;
    }

    /**
     * Envía un correo electrónico utilizando mailer
     *
     * @param array $dest: Arreglo con la forma array(
     *                                           'email destinatario 1' => 'nombre destinatario 1',
     *                                           'email destinatario 2' => 'nombre destinatario 2'
     *                                            )
     * @param array $content: Arreglo con el contenido por secciones con la forma
     *                                           array(
     *                                               '{{title}}' => 'Título',
     *                                               '{{content}}' => '<p>Contenido</p><p>Etc...</p>',
     *                                           )
     * @param int $template: Template elegido, por defecto es el primero (0)
     * @param array $adj: Arreglo con direccion local de los adjuntos a enviar, con la forma array(
     *                                                                                       'ruta archivo 1',
     *                                                                                       'ruta archivo 2'
     *                                                                                       )
     *
     * @throws \RuntimeException en caso de algún problema
     * @return bool true si fue enviado correctamente, false si no
     */
    public static function send(array $dest, array $content, int $template = 0, array $adj = []) : bool {
        global $config;

        # Hack para cuando algun servidor tenga un SSL no válido, por ejemplo localhost
        $https['ssl']['verify_peer'] = false;
        $https['ssl']['verify_peer_name'] = false;
        $https['ssl']['allow_self_signed'] = true;

        # Verificar si están llenos los campos
        if(!$config['build']['production'] && Functions::emp($config['mailer']['host'])) {
            throw new \RuntimeException('Los datos de mailer, en Ocrend.ini.yml están vacíos.');
        }

        # Transporte
        if ($config['mailer']['requiere_validacion']){
            $transport = (new \Swift_SmtpTransport($config['mailer']['host'], $config['mailer']['port'], 'tls'))
            ->setUsername($config['mailer']['user'])
            ->setPassword($config['mailer']['pass'])
            ->setStreamOptions($https);
        }else{
            $transport = (new \Swift_SmtpTransport($config['mailer']['host'], $config['mailer']['port']))
            ->setStreamOptions($https);
        }
        
        # Mailer
        $mailer = new \Swift_Mailer($transport);
       
        # El mensaje
        $message = new \Swift_Message('test');

        array_key_exists('{{logo}}',$content) ? $logo = $message->embed(Swift_Image::fromPath($content['{{logo}}'])): $logo = ""; 
        array_key_exists('{{logo_udd}}',$content) ? $logo_udd = $message->embed(Swift_Image::fromPath($content['{{logo_udd}}'])) : $logo_udd = "";;
        
        $content_final = self::loadTemplate($content,$template);

        $content_final = str_replace('{{logo_new}}',$logo,$content_final);
        $content_final = str_replace('{{logo_udd}}',$logo_udd,$content_final);

        $message->setSubject(array_key_exists('{{title}}',$content) ? $content['{{title}}'] : $config['build']['name']);
        $message->setBody($content_final, 'text/html');
        $message->setFrom([ $config['mailer']['user'] => $config['build']['name']]);
        $message->setTo($dest);

        # Adjuntos
        if (sizeof($adj)) {
            foreach ($adj as $ruta) {
                $message->attach(\Swift_Attachment::fromPath($ruta));
            }
        }   

        # Verificar respuesta
        return (bool) $mailer->send($message);
    }
}
