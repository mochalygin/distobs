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

        $node = $app['node']->findBy(array('public_key' => $nodeKey));

        if (! $node) {
            $app['monolog']->info('This is new Node');

            $nodeByUrl = $app['node']->findBy(array('url' => $nodeUrl));
            if ( $nodeByUrl )
                $app['monolog']->warning('There was different Node at this URL');
            else
                $app['monolog']->info('This is new Node URL');
        }

        return new JsonResponse (array('result'=>array('nodeName' => $app['settings']->load('nodeName'))));
    }

}
