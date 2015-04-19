<?php

namespace DistObsNet\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TouchNodeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('node:touch')
            ->setDescription('Touching Node')
            ->addArgument(
                'key',
                InputArgument::REQUIRED,
                'Key of Node'
            )
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'URL of Node'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $enterNodeUrl = $input->getArgument('url');
        $enterNodeKey = $input->getArgument('key');

        $myNodeUrl = $container['settings']->load('nodeUrl')->value;
        $myNodeKey = $container['key']->publicKey();

        $container['monolog']->addInfo('Touching Node with key "' . $enterNodeKey . '" by URL ' . $enterNodeUrl);

        $result = file_get_contents($enterNodeUrl . '/publisher/handshake?nodeUrl=' . urlencode($myNodeUrl) . '&nodeKey=' . $myNodeKey);

        if (! $data = json_decode($result)) {
            $container['monolog']->error('Incorrect answer from Node');
            return;
        }
    }
}
