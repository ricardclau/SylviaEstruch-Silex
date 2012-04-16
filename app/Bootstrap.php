<?php

class Bootstrap
{
    /**
     * @var Silex\Application
     */
    private $app;

    public function __construct($app)
    {
        $this->app = $app;

        $this->registerProviders();
        $this->configApp();
        $this->routing();
    }

    public function run()
    {
        $this->app->run();
    }

    private function configApp()
    {
        $app = $this->app;
        $app['debug'] = true;

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
        });
    }

    private function registerProviders()
    {
        $this->app->register(new Silex\Provider\UrlGeneratorServiceProvider());
        $this->app->register(new Silex\Provider\SessionServiceProvider());
        $this->app->register(new Silex\Provider\TwigServiceProvider(), array(
            'twig.class_path' => __DIR__ . '/../vendor/twig/twig/lib',
            'twig.path'       => array(
                __DIR__ . '/../src/SylviaEstruch/Resources/views',
            ),
            'twig.options' => array(
                'cache' => __DIR__ . '/cache/twig',
            ),
        ));
        $this->app['twig']->addExtension(new Symfony\Bridge\Twig\Extension\RoutingExtension($this->app['url_generator']));
    }

    private function routing()
    {
        $this->staticRoutes();
    }

    private function staticRoutes()
    {
        $app = $this->app;

        $app->get('/', function (Silex\Application $app) {
            $locale = $app['request']->getPreferredLanguage(array('ca', 'es', 'en'));

            return $app->redirect(
                        $app['url_generator']->generate('homepage', array('locale' => $locale))
                   );
        });

        $app->get('/{locale}/', function (Silex\Application $app) {
            return $app['twig']->render('static/home.html.twig');
        })->bind('homepage');
    }
}