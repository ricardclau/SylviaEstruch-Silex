<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * @var Silex\Application
 */
$app = new Application();

/**
 * Configuration
 */
$app['debug'] = true;
$app['config.locales'] = array('ca', 'es', 'en');
$app['config.locales.regexp'] = 'ca|es|en';

$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'dbname'   => 'sylviaestruch',
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => null,
    'charset'  => 'utf8',
);

/**
 * Providers
 */
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\SwiftmailerServiceProvider(), array(
    'swiftmailer.class_path'  => __DIR__.'/../vendor/swiftmailer/swiftmailer/lib/classes',
));
$app['swiftmailer.transport'] = \Swift_SendmailTransport::newInstance();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'       => array(
        __DIR__ . '/../src/SylviaEstruch/Resources/views',
    ),
    'twig.options' => array(
        'cache' => __DIR__ . '/../data/cache/twig',
    ),
));
$app['twig']->addExtension(new Symfony\Bridge\Twig\Extension\RoutingExtension($app['url_generator']));
$app['twig']->addExtension(new SylviaEstruch\Twig\Extension\WebExtension());

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
    ));

    $app['translator.domains'] = array('messages' => array(
        'ca' => __DIR__ . '/../src/SylviaEstruch/Resources/translations/messages.ca.yml',
        'es' => __DIR__ . '/../src/SylviaEstruch/Resources/translations/messages.es.yml',
        'en' => __DIR__ . '/../src/SylviaEstruch/Resources/translations/messages.en.yml',
    ));
    $app['translator.loader'] = new Symfony\Component\Translation\Loader\YamlFileLoader();

    $app['translator']->addLoader('xlf', new Symfony\Component\Translation\Loader\XliffFileLoader());
    $app['translator']->addResource('xlf', __DIR__ . '/../vendor/symfony/framework-bundle/Symfony/Bundle/FrameworkBundle/Resources/translations/validators.es.xlf', 'es', 'validators');
    $app['translator']->addResource('xlf', __DIR__ . '/../vendor/symfony/framework-bundle/Symfony/Bundle/FrameworkBundle/Resources/translations/validators.ca.xlf', 'ca', 'validators');
    $app['translator']->addResource('xlf', __DIR__ . '/../vendor/symfony/framework-bundle/Symfony/Bundle/FrameworkBundle/Resources/translations/validators.en.xlf', 'en', 'validators');

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

return $app;