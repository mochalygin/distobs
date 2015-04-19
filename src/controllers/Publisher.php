<?php

namespace controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class Publisher
{

    public function index(Request $request, Application $app)
    {
        return new Response($app['twig']->render('publisher/index.twig', array()));
    }

    public function handshake(Request $request, Application $app)
    {
        $nodeUrl = $request->query->get('nodeUrl');
        $nodeKey = $request->query->get('nodeKey');
        $app['monolog']->info('Handshake from URL: ' . $nodeUrl . ', key: ' . $nodeKey);

        if (!$nodeUrl || !$nodeKey) {
            $app['monolog']->error('Bad handshake');
            return new JsonResponse (array('error'=>array('msg' => 'Bad URL or Key')));
        }

        $node = $app['node']->findBy(['public_key' => $nodeKey]);

        if (! $node) {

        }

        return new JsonResponse (array('result'=>array('nodeKey' => $app['key']->publicKey())));
    }

}
