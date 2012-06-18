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
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app['swiftmailer.transport'] = $app->share(function () use ($app) {
    return \Swift_SendmailTransport::newInstance();
});

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

    $app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
        $translator->addLoader('yaml', new Symfony\Component\Translation\Loader\YamlFileLoader());

        $translator->addResource('yaml', __DIR__ . '/../src/SylviaEstruch/Resources/translations/messages.es.yml', 'es', 'messages');
        $translator->addResource('yaml', __DIR__ . '/../src/SylviaEstruch/Resources/translations/messages.ca.yml', 'ca', 'messages');
        $translator->addResource('yaml', __DIR__ . '/../src/SylviaEstruch/Resources/translations/messages.en.yml', 'en', 'messages');

        $translator->addResource('xliff', __DIR__ . '/../vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.es.xlf', 'es', 'validators');
        $translator->addResource('xliff', __DIR__ . '/../vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.ca.xlf', 'ca', 'validators');
        $translator->addResource('xliff', __DIR__ . '/../vendor/symfony/form/Symfony/Component/Form/Resources/translations/validators.en.xlf', 'en', 'validators');

        return $translator;
    }));

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