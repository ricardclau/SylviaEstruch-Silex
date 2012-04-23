<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

/**
 * @var Silex\Application
 */
$app = new Application();

/**
 * Configuration
 */
$app['debug'] = true;
$app['config.langs'] = array('ca', 'es', 'en');
$app['config.langs.regexp'] = array('ca|es|en');
/**
 * Charset?
 */
$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'dbname'   => 'sylviaestruch',
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => null,
    'charset'  => 'utf-8',
);

/**
 * Providers
 */
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider());
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

/**
 * Before filter setting language from URL
 */
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

/**
 * Static URLS
 */
$app->get('/', function (Silex\Application $app) {
    $locale = $app['request']->getPreferredLanguage($app['config.langs']);

    return $app->redirect(
        $app['url_generator']->generate('homepage', array('locale' => $locale))
    );
});

$app->get('/{locale}/', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})->bind('homepage');

$app->get('/{locale}/frases.js', function (Silex\Application $app) {
    // @todo Set proper HTTP cache headers
    $content = $app['twig']->render('static/frases.js.twig');
    return new Response($content, 200, array('content-type' => 'application/javascript'));
})->bind('frasesjs');

$app->get('/{locale}/contacto', function (Silex\Application $app) {
    return $app['twig']->render('static/contacto.html.twig');
})->bind('contacto');

$app->get('/{locale}/biografia', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})->bind('biografia');

$app->get('/{locale}/pintura', function (Silex\Application $app) {
    $pinturaService = new \SylviaEstruch\Service\PinturaService($app['db']);
    $cats = $pinturaService->getCategories();
    $paintings = $pinturaService->getCategoryPaintings(6);

    return $app['twig']->render('pintura/categoria.html.twig', array(
        'cats' => $cats,
    ));
})->bind('pintura');

$app->get('/{locale}/pintura/{id}/{slug}', function (Silex\Application $app, $id) {
    $pinturaService = new \SylviaEstruch\Service\PinturaService($app['db']);
    $cats = $pinturaService->getCategories();
    $paintings = $pinturaService->getCategoryPaintings($id);

    return $app['twig']->render('pintura/categoria.html.twig', array(
        'cats' => $cats,
    ));
})->bind('pintura_categoria');

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