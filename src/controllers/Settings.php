<?php

namespace controllers;

use Silex\Application;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

use DistObsNet\Console\CreateDBCommand;
use DistObsNet\Console\CreateKeyCommand;

class Settings
{
    public function index(Request $request, Application $app)
    {
        $data = array();
        $data['title'] = 'Settings';
        $data['publicKey'] = $app['key']->publicKey();

        try {
            $data['nodeName'] = $app['settings']->load('nodeName');
            $data['nodeUrl'] = $app['settings']->load('nodeUrl');
            $data['inviteNode'] = $app['settings']->load('inviteNode');

//            $data['observers'] = $app['observer']->loadAll();
//            $data['publishers'] = $app['publisher']->loadAll();

        } catch (\Exception $e) {
            $data['nodeName'] = false;
        }

        return new Response($app['twig']->render(
                'settings/index.twig',
                $data
            )
        );
    }

    public function initKey(Request $request, Application $app)
    {
        $command = new CreateKeyCommand;
        $command->setContainer($app);

        $input = new ArrayInput(array());
        $output = new NullOutput;

        $result = $command->run($input, $output);

        return $app->redirect($app['url_generator']->generate('settings'));
    }

    public function installDb(Request $request, Application $app)
    {
        $command = new CreateDBCommand;
        $command->setContainer($app);

        $url = substr (
            $request->server->get('REQUEST_SCHEME')
                . '://'
                . $request->server->get('HTTP_HOST')
                . $request->server->get('REQUEST_URI'),
            0,
            -(strlen('settings/installDb')) //cut '/settings' suffix
        );
        $input = new ArrayInput(array('url' => $url));
        $output = new NullOutput;

        $result = $command->run($input, $output);

        return $app->redirect($app['url_generator']->generate('settings'));
    }

    public function nodeUrl(Request $request, Application $app)
    {
        if ($nodeUrl = $request->request->get('nodeUrl')) {

            if (! $model = $app['settings']->load('nodeUrl')) {
                $model = $app['settings']
                    ->setCode('nodeUrl');
            }
            $model->setValue($nodeUrl);

            if ($model->save())
                $app['monolog']->addInfo('New node URL was saved');
            else
                $app['monolog']->addError('Error while saving node URL (' . $nodeUrl . ')');
        }

        return $app->redirect($request->server->get('HTTP_REFERER'));
    }

    public function nodeName(Request $request, Application $app)
    {
        if ($nodeName = $request->request->get('nodeName')) {

            if (! $model = $app['settings']->load('nodeName')) {
                $model = $app['settings'];
                $model->code = 'nodeName';
            }
            $model->value = $nodeName;

            if ($model->save())
                $app['monolog']->addInfo('New node name was saved');
            else
                $app['monolog']->addError('Error while saving node name (' . $nodeName . ')');
        }

        return $app->redirect($request->server->get('HTTP_REFERER'));
    }

}
