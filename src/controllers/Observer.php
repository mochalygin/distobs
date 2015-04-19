<?php

namespace controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Publisher
{
    public function index(Request $request, Application $app)
    {
        return new Response($app['twig']->render('observer/index.twig', array()));
    }
}
