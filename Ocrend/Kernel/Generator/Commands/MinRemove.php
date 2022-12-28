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

/**
 * Comando para crear modelos
 * 
 * @author Jorge Jara H. <jjara@wys.cl>
 */
class MinRemove extends Command {

    protected function configure() {
        $this
            ->setName('min:remove')
            ->setDescription('Elimina archivos .min.js de la carpeta assets/jscontrollers')
            ->setHelp('Este comando se ocupa para eliminar los archivos min.js de la carpeta assets/jscontrollers')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        #obtener la ruta del este archivo
        $path = dirname(__FILE__);
        #ejecutar archivo python para minificar
        $output->writeln(shell_exec('python '.$path.'\.\MinificadorJS.py "remove"'));
        $output->writeln('Archivos .min.js eliminados');
    }
}