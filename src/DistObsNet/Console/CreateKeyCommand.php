<?php

namespace DistObsNet\Console;

//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateKeyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('key')
            ->setDescription('Creating private key')
//            ->addArgument(
//                'name',
//                InputArgument::OPTIONAL,
//                'Who do you want to greet?'
//            )
//            ->addOption(
//               'yell',
//               null,
//               InputOption::VALUE_NONE,
//               'If set, the task will yell in uppercase letters'
//            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $name = $input->getArgument('name');
//        if ($name) {
//            $text = 'Hello '.$name;
//        } else {
//            $text = 'Hello';
//        }
//
//        if ($input->getOption('yell')) {
//            $text = strtoupper($text);
//        }

//        $output->writeln($text);

        $container = $this->getContainer();
        $output->writeln('Public key: ' . $container['keyManager']->providePkey() . PHP_EOL);
        $container['monolog']->addInfo('Public key is: ' . $container['keyManager']->providePkey() . PHP_EOL);
    }

}
