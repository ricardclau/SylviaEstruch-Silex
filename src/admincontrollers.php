<?php

$users = array(
    'test' => 'test',
);

$mustBeAnonymous = function (Request $request) use ($app) {
    if ($app['session']->has('userId')) {
        return $app->redirect('/admin/logout');
    }
};

$mustBeLogged = function (Request $request) use ($app) {
    if (!$app['session']->has('userId')) {
        return $app->redirect('/admin/login');
    }
};

$app->get('/admin/logout', function (Silex\Application $app) {
    $app['session']->clear();
    return $app->redirect('/admin/login');
});

$app->get('/admin/login', function (Silex\Application $app) {
    return $app['twig']->render('admin/login.html.twig');
})
->middleware($mustBeAnonymous);

$app->post('/admin/login', function (Silex\Application $app) {
    $loginok = false;

    $login = $app['request']->get('login');
    $password = $app['request']->get('password');

    if (isset($users[$login]) && $users[$login] === $password) {
        $loginok = true;
        $app['session']->set('userId', $login);
    }

    if ($loginok) {
        return $app->redirect('/admin/menu');
    } else {
        return $app->redirect('/admin/login');
    }
})
->middleware($mustBeAnonymous);
