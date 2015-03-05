<?php

namespace DistObsNet;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Settings
{
    public function index(Request $request, Application $app)
    {
        if ($app['key']->isInit())
            $render = $app['twig']->render('settings/keyInited.twig', array('publicKey' => $app['key']->getPublicKey()));
        else
            $render = $app['twig']->render('settings/keyNeedInit.twig');
        return new Response($render);
    }    
    
}
