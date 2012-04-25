<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;

/**
 * Index URL, automatic redirect to preferred user locale
 */
$app->get('/', function (Silex\Application $app) {
    $locale = $app['request']->getPreferredLanguage($app['config.locales']);

    return $app->redirect(
        $app['url_generator']->generate('homepage', array('locale' => $locale))
    );
});

/**
 * Home page
 */
$app->get('/{locale}/', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})
->bind('homepage')
->assert('locale', $app['config.locales.regexp']);

/**
 * Localized javascript dictionaries
 * Uses browser cache
 */
$app->get('/{locale}/frases.js', function (Silex\Application $app) {
    $content = $app['twig']->render('static/frases.js.twig');
    $response = new Response($content, 200, array('content-type' => 'application/javascript'));
    $response->setMaxAge(600);
    $response->setSharedMaxAge(600);
    return $response;
})
->bind('frasesjs')
->assert('locale', $app['config.locales.regexp']);

/**
 * Renders contact form
 */
$app->get('/{locale}/contacto', function (Silex\Application $app) {
    return $app['twig']->render('static/contacto.html.twig');
})
->bind('contacto')
->assert('locale', $app['config.locales.regexp']);

/**
 * Sends e-mail and returns a json response
 */
$app->post('/{locale}/contacto', function (Silex\Application $app) {
    $contactData = array(
        'nombre' => $app['request']->get('nombre'),
        'email'  => $app['request']->get('email'),
        'texto'  => $app['request']->get('texto'),
    );

    $collectionConstraint = new Constraints\Collection(array(
        'nombre' => array(
                        new Constraints\NotNull(),
                        new Constraints\NotBlank(),
                        ),
        'email'  => array(
                        new Constraints\NotNull(),
                        new Constraints\NotBlank(),
                        new Constraints\Email()
                        ),
        'mensaje' => array(
                        new Constraints\NotNull(),
                        new Constraints\NotBlank()
                        ),
    ));

    $errors = $app['validator']->validateValue($contactData, $collectionConstraint);

    if (0 === count($errors)) {
        return $app->json(array('msg' => 'OK'), 200, array('content-type' => 'application/json'));
    } else {
        return $app->json(array('msg' => $errors[0]->getMessage()), 400, array('content-type' => 'application/json'));
    }
})
->bind('contacto_enviar');

/**
 * Biography section
 */
$app->get('/{locale}/biografia', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})
->bind('biografia')
->assert('locale', $app['config.locales.regexp']);

/**
 * Paintings section (defaults to newest paintings)
 */
$app->get('/{locale}/pintura', function (Silex\Application $app) {
    $pinturaService = new \SylviaEstruch\Service\PinturaService($app['db']);
    $cats = $pinturaService->getCategories();
    $paintings = $pinturaService->getCategoryPaintings(6);

    return $app['twig']->render('pintura/categoria.html.twig', array(
        'cats' => $cats,
        'paintings' => $paintings,
    ));
})
->bind('pintura')
->assert('locale', $app['config.locales.regexp']);

/**
 * Painting specific category page
 */
$app->get('/{locale}/pintura/{id}/{slug}', function (Silex\Application $app, $id) {
    $pinturaService = new \SylviaEstruch\Service\PinturaService($app['db']);
    $cats = $pinturaService->getCategories();
    $paintings = $pinturaService->getCategoryPaintings($id);

    return $app['twig']->render('pintura/categoria.html.twig', array(
        'cats' => $cats,
        'paintings' => $paintings,
    ));
})
->bind('pintura_categoria')
->assert('locale', $app['config.locales.regexp']);

/**
 * Theater section (defaults to first category)
 */
$app->get('/{locale}/teatro', function (Silex\Application $app) {
    $teatroService = new \SylviaEstruch\Service\TeatroService($app['db']);
    $cats = $teatroService->getCategories();
    $teatros = $teatroService->getCategoryPerformances(1);

    return $app['twig']->render('teatro/categoria.html.twig', array(
        'cats' => $cats,
        'teatros' => $teatros,
    ));
})
->bind('teatro')
->assert('locale', $app['config.locales.regexp']);

/**
 * Theater specific category web page
 */
$app->get('/{locale}/teatro/{id}/{slug}', function (Silex\Application $app, $id) {
    $teatroService = new \SylviaEstruch\Service\TeatroService($app['db']);
    $cats = $teatroService->getCategories();
    $teatros = $teatroService->getCategoryPerformances($id);

    return $app['twig']->render('teatro/categoria.html.twig', array(
        'cats' => $cats,
        'teatros' => $teatros,
    ));
})
->bind('teatro_categoria')
->assert('locale', $app['config.locales.regexp']);

/**
 * Renders design category
 */
$app->get('/{locale}/diseño', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})
->bind('disenyo')
->assert('locale', $app['config.locales.regexp']);

/**
 * Renders restoration category
 */
$app->get('/{locale}/restauracion', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})
->bind('restauracion')
->assert('locale', $app['config.locales.regexp']);
