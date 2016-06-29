<?php

namespace SoapBox\Raven\Commands;

use SoapBox\Raven\Utils\DispatcherCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Install, serve, migrate, refresh, halt an elasticsearch server.
 */
class ElasticCommand extends DispatcherCommand
{
    protected $command       = 'elastic';
    protected $description   = 'Boot and index elasticsearch.';
    protected $asciiGreeting = "
            _           _   _                              _
           | |         | | (_)                            | |
        ___| | __ _ ___| |_ _  ___ ___  ___  __ _ _ __ ___| |__
       / _ \ |/ _` / __| __| |/ __/ __|/ _ \/ _` | '__/ __| '_ \
      |  __/ | (_| \__ \ |_| | (__\__ \  __/ (_| | | | (__| | | |
       \___|_|\__,_|___/\__|_|\___|___/\___|\__,_|_|  \___|_| |_|



     /)/)/) /).-')
    ////((.'_.--'   .(\(\(\                   n/(/.')_         .
   ((((_/ .'      .-`)))))))                  `-._ ('.'        \`(\
  (_._ ` (         `.   (/ |                      \ (           `-.\
      `-. \          `-.  /                        `.`.           \ \
         `.`.          | /                /)         \ \           | L
           `.`._.      ||_               (()          `.\          ) F
   (`._      `. <    .'.-'                \`-._____    ||        .' /
    `(\`._.._(\(\)_.'.'-------------.___   `-.(`._ `-./ /     _.' .'
      (.-.| \_`.__.-<     `.    . .-'   `-.   _> `-._((`.__.-'_.-'
          (.--'   ' |    \ \     /| \.-./ |\ `-.   _.'>.___,-'`.
             (  o  <      |     |  `o   o'  |  /(`'.-'   --.    \
           .'     /      .'   _ |   |   |   |  ( .'/  o .-'   \  |
           (__.-.`-._  -'    '   \  \   /  /    ' /    _/      | J
                 \_  `.      _.__.L |   | J      (  .'\`.    _/-./
                   `-<  .-L|'`-|  ||\\V/ ||       `'   L \  /   /
                      |J  ||    \ ||||  |||            |  |_|  )
                      ||  ||     )||||  |||            || / ||J
                      (|  (|    / |||)  (||            |||  |||
                      ||  ||   / /||||  |||            |(|  |||
                      ||  ||  / / ||||  |||            |||  |||
_______.------.______/ |_/ |_/_|_/// |__| \\__________// |--( \\---------
                    '-' '-'       '-'    `-`           '-'   `-`
    ";

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->asciiGreeting);

        parent::execute($input, $output);
    }
}
