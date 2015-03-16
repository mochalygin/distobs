<?php

namespace DistObsNet\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Settings
{
    public function index(Request $request, Application $app)
    {
        $data = array();
        $data['publicKey'] = $app['key']->publicKey();

        try {
            $data['isDbCreated'] = $app['settings']->load('isDbCreated');

            if ($nodeName = $app['settings']->load('nodeName'))
                $data['nodeName'] = $nodeName->value;

            if ($data['nodeUrl'] = $app['settings']->load('nodeUrl')) {
                $data['nodeUrl'] = $data['nodeUrl']->value;
                $data['alterNodeUrl'] = $data['nodeUrl'];
            } else {
                $data['alterNodeUrl'] = substr (
                        $request->server->get('REQUEST_SCHEME')
                            . '://'
                            . $request->server->get('HTTP_HOST')
                            . $request->server->get('REQUEST_URI'),
                        0,
                        -8 //cut '/settings' word
                );
            }

        } catch (\Exception $e) {
            $data['isDbCreated'] = false;
        }


        return new Response($app['twig']->render(
                'settings/index.twig',
                $data
            )
        );
    }

    public function initKeys(Request $request, Application $app)
    {
        return $app->redirect($app['url_generator']->generate('settings'));
    }

    public function installDb(Request $request, Application $app)
    {
        exec('../bin/console db:install');

        return $app->redirect($app['url_generator']->generate('settings'));
    }

    public function nodeUrl(Request $request, Application $app)
    {
        if ($nodeUrl = $request->request->get('nodeUrl')) {

            if (! $model = $app['settings']->load('nodeUrl')) {
                $model = $app['settings']
                    ->create()
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
                $model = $app['settings']
                    ->create()
                    ->setCode('nodeName');
            }
            $model->setValue($nodeName);

            if ($model->save())
                $app['monolog']->addInfo('New node name was saved');
            else
                $app['monolog']->addError('Error while saving node name (' . $nodeName . ')');
        }

        return $app->redirect($request->server->get('HTTP_REFERER'));
    }

}
