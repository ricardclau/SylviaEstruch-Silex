<?php

use Silex\Application;

$app = new Application();
$app['debug'] = true;
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.class_path' => __DIR__ . '/../vendor/twig/twig/lib',
    'twig.path'       => array(
        __DIR__ . '/../src/SylviaEstruch/Resources/views',
    ),
    'twig.options' => array(
        'cache' => __DIR__ . '/../data/cache/twig',
    ),
));
$app['twig']->addExtension(new Symfony\Bridge\Twig\Extension\RoutingExtension($app['url_generator']));
$app->before(function () use ($app) {
    /**
     * This must be done here as access to $app['request'] is restricted outside this scope
     */
    if ($locale = $app['request']->get('locale')) {
        $app['locale'] = $locale;
        $app['session']->set('locale', $locale);
    }

    /**
     * Translations must be set AFTER configuring $app['locale']
     */
    $app->register(new Silex\Provider\TranslationServiceProvider(), array(
        'locale_fallback' => 'en', // Default locale
        'translation.class_path' => __DIR__ . '/../vendor/symfony/translation',
    ));

    $app['translator.loader'] = new Symfony\Component\Translation\Loader\YamlFileLoader();
    $app['translator.messages'] = array(
        'ca' => __DIR__ . '/../src/SylviaEstruch/Resources/translations/messages.ca.yml',
        'es' => __DIR__ . '/../src/SylviaEstruch/Resources/translations/messages.es.yml',
        'en' => __DIR__ . '/../src/SylviaEstruch/Resources/translations/messages.en.yml',
    );

    /**
     * Translator must be sent to Twig after setting up everything
     */
    $app['twig']->addExtension(new Symfony\Bridge\Twig\Extension\TranslationExtension($app['translator']));

    /**
     * Global Twig templates must be defined once everything is set
     */
    $app['twig']->addGlobal('layout', $app['twig']->loadTemplate('layout.html.twig'));
    $app['twig']->addGlobal('adminlayout', $app['twig']->loadTemplate('adminlayout.html.twig'));
});


$app->get('/', function (Silex\Application $app) {
    $locale = $app['request']->getPreferredLanguage(array('ca', 'es', 'en'));

    return $app->redirect(
        $app['url_generator']->generate('homepage', array('locale' => $locale))
    );
});

$app->get('/{locale}/', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})->bind('homepage');

$app->get('/{locale}/frases.js', function (Silex\Application $app) {
    // Set Proper Content-Type
    // Also set Expires Headers
    return $app['twig']->render('static/frases.js.twig');
})->bind('frasesjs');

$app->get('/{locale}/contacto', function (Silex\Application $app) {
    return $app['twig']->render('static/contacto.html.twig');
})->bind('contacto');

$app->get('/{locale}/biografia', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})->bind('biografia');

$app->get('/{locale}/pintura', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})->bind('pintura');

$app->get('/{locale}/teatro', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})->bind('teatro');

$app->get('/{locale}/diseño', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})->bind('disenyo');

$app->get('/{locale}/restauracion', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})->bind('restauracion');

return $app;