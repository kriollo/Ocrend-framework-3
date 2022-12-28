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
use Symfony\Component\Console\Input\InputOption;
use Ocrend\Kernel\Helpers as Helper;

/**
 * Comando para minificar archivos js y pasar a produccion el aplicativo
 * 
 * @author Jorge Jara H. <jjara@wys.cl>
 */
class MinBuild extends Command {

    protected function configure() {
        $this
            ->setName('min:build')
            ->setDescription('Minificar y comprimir archivos assets/jscontrollers')
            ->setHelp('Este comando se ocupa para minificar los archivos js de la carpeta assets/jscontrollers')
            ->addOption('prod', null, InputOption::VALUE_OPTIONAL, 'Si se especifica un modelo, saber si se conectara con la base de datos', 1)
            ->addOption('dev', null, InputOption::VALUE_OPTIONAL, 'Si se especifica un modelo, saber si se conectara con la base de datos', 1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        #obtener la ruta del este archivo
        $path = dirname(__FILE__);

        #validar si no se especifica --prod o --dev
        if((1 == $input->getOption('prod')) && (1 == $input->getOption('dev'))){
            $output->writeln('');
            $output->writeln('********** ERROR **********');
            $output->writeln('');
            $output->writeln('No se especifico --prod o --dev');
            $output->writeln('');
            $output->writeln('***************************');
            $output->writeln('');
            return;
        }

        #validar que no se especifique --prod y --dev al mismo tiempo
        if(("" == $input->getOption('prod')) && ("" == $input->getOption('dev'))){
            $output->writeln('');
            $output->writeln('********** ERROR **********');
            $output->writeln('');
            $output->writeln('No se puede especificar --prod y --dev al mismo tiempo');
            $output->writeln('');
            $output->writeln('***************************');
            $output->writeln('');
            return;
        }

        $root = dirname(__DIR__, 4);

        $result = Helper\Files::findFile($root, 'Config/Ocrend.ini.yml', dirname(__DIR__));

        if (1 !== $input->getOption('prod')){
            $output->writeln('Editando archivo Ocrend.ini.yml para dejar en modo produccion');
            $file = file_get_contents($result);

            #cambiamos modo de twig en archivo de configuracion
            $file = str_replace('cache: false', 'cache: true', $file);
            $file = str_replace('strict_variables: true', 'strict_variables: false', $file);

            #camibamos modo de produccion en archivo de configuracion
            $file = str_replace('production: false', 'production: true', $file);
            file_put_contents($result, $file);

            $output->writeln(shell_exec('python '.$path.'\.\MinificadorJS.py'));
            $output->writeln('Archivos .min.js Creados');
        }
        if (1 !== $input->getOption('dev')){
            $output->writeln('Editando archivo Ocrend.ini.yml para dejar en modo desarrollo');
            $file = file_get_contents($result);

            #cambiamos modo de twig en archivo de configuracion
            $file = str_replace('cache: true', 'cache: false', $file);
            $file = str_replace('strict_variables: false', 'strict_variables: true', $file);

            #camibamos modo de produccion en archivo de configuracion
            $file = str_replace('production: true', 'production: false', $file);
            file_put_contents($result, $file);

            $output->writeln(shell_exec('python '.$path.'\.\MinificadorJS.py "remove"'));
            $output->writeln('Archivos .min.js Eliminados');
        }
    }
}