<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function() use ($app) {
    return $app['twig']->render('index.twig', array());
})
->bind('homepage');

/* Settings */
$app->get('/settings', 'controllers\\Settings::index')
    ->bind('settings');
$app->get('/settings/initKey', 'controllers\\Settings::initKey')
    ->bind('settings.initKey');
$app->get('/settings/installDb', 'controllers\\Settings::installDb')
    ->bind('settings.installDb');
$app->post('/settings/nodeUrl', 'controllers\\Settings::nodeUrl');
$app->post('/settings/nodeName', 'controllers\\Settings::nodeName');

/*   */
$app->get('/observer', 'controllers\\Observer::index');

/* Errors */
$app->error(function(\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html',
        'errors/'.substr($code, 0, 2).'x.html',
        'errors/'.substr($code, 0, 1).'xx.html',
        'errors/default.html',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});

$app->before(function (Request $request) use ($app) {
    if (strpos($request->getRequestUri(), 'settings') === false)
        return new RedirectResponse('settings');
});