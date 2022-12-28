<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Ocrend\Kernel\Generator\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Información inicial de comandos
 * 
 * @author Brayan Narváez <prinick@ocrend.com>
 */
class Info extends Command {

    protected function configure() {
        $this
        ->setName('info')
        ->setDescription('Da información acerca del generador')
        ->setHelp('Este comando se ocupa de generar información acerca de la consola');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        global $config;

        $output->writeln([
            '',
            'COMANDOS - ' . $config['build']['name'],
            '',
            '--------------------------------------',
            '',
            'app:',
            '   app:m [Nombre] [Argumentos] [Opciones] Crea un modelo',
            '   app:v [Nombre] [Argumentos] [Opciones] Crea nueva vista',
            '   app:c [Nombre] [Argumentos] [Opciones] Crea nuevo controlador',
            '   argumentos: El orden es irrelevante y deben ir juntos, sin separacion entre ellos ',
            '       m: Crea un modelo',
            '       v: Crea una vista',
            '       c: Crea un controlador',
            '   opciones: El orden es irrelevante y deben estar separadas entre espacios',
            '       --db: Si se crea un modelo, es capaz de conectar con la base de datos',
            '       --ajax: Si se crea una vista y modelo, se realiza una conexion por ajax',
            '',
            'users:',
            '   users:install Instala todo el sistema de usuarios',
            '',
            '',
            'min:',
            '   min:build: Ejecuta Script Minificador en Python de archivos JS de la carpeta assets/jscontrollers',
            '   opciones: Sólo debe ir uno de los siguientes',
            '       --prod: Modo producción',
            '       --dev : Modo desarrollo',
            '   min:remove: Elimina los archivos minificados de la carpeta assets/jscontrollers, devuelve a modo desarrollo',
            '',
            '',
        ]);
    }
}