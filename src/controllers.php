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
$app->get('/{locale}/{section}', function (Silex\Application $app) {
    return $app['twig']->render('static/contacto.html.twig');
})
->bind('contacto')
->assert('locale', $app['config.locales.regexp'])
->assert('section', 'contacto|contacte|contact');

/**
 * Sends e-mail and returns a json response
 */
$app->post('/{locale}/{section}', function (Silex\Application $app) {
    $contactData = array(
        'nombre' => $app['request']->get('nombre'),
        'email'  => $app['request']->get('email'),
        'mensaje'  => $app['request']->get('mensaje'),
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
        require_once __DIR__ . '/../vendor/swiftmailer/swiftmailer/lib/swift_init.php';

        $message = \Swift_Message::newInstance()
            ->setSubject('Missatge rebut des de la web www.sylviaestruch.com')
            ->setFrom(array($contactData['email'] => $contactData['nombre']))
            ->setTo('ricard.clau@gmail.com')
            ->setBody($contactData['mensaje'])
                ;

        $app['mailer']->send($message);

        return $app->json(array('msg' => $app['translator']->trans('contacto.mailok')), 200, array('content-type' => 'application/json'));
    } else {
        $jsonerr = array();
        foreach ($errors as $error) {
            $jsonerr[$error->getPropertyPath()][] = $app['translator']->trans($error->getMessage(), array(), 'validators');
        }
        return $app->json(array('msg' => 'ERROR', 'errors' => $jsonerr), 400, array('content-type' => 'application/json'));
    }
})
->bind('contacto_enviar')
->assert('locale', $app['config.locales.regexp'])
->assert('section', 'contacto|contacte|contact');;

/**
 * Biography section
 */
$app->get('/{locale}/{section}', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})
->bind('biografia')
->assert('locale', $app['config.locales.regexp'])
->assert('section', 'biografia|biografía|biography');

/**
 * Painting page, works both for specific id and even without id and slug (which defaults to category 6)
 */
$app->get('/{locale}/{section}/{id}/{slug}', function (Silex\Application $app, $id) {
    $pinturaService = new \SylviaEstruch\Service\PinturaService($app['db']);

    $cat = $pinturaService->getCategory($id);
    if (empty($cat)) {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Category does not exist');
    }

    $cats = $pinturaService->getCategories();
    $paintings = $pinturaService->getCategoryPaintings($id);

    return $app['twig']->render('pintura/categoria.html.twig', array(
        'cats' => $cats,
        'paintings' => $paintings,
    ));
})
->bind('pintura')
->value('id', 6)
->value('slug', null)
->assert('locale', $app['config.locales.regexp'])
->assert('section', 'pintura|painting');

/**
 * Theater page, works both for specific category web page and generic theater page (which defaults to category 1)
 */
$app->get('/{locale}/{section}/{id}/{slug}', function (Silex\Application $app, $id) {
    $teatroService = new \SylviaEstruch\Service\TeatroService($app['db']);
    $cat = $teatroService->getCategory($id);
    if (empty($cat)) {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Category does not exist');
    }
    $cats = $teatroService->getCategories();
    $teatros = $teatroService->getCategoryPerformances($id);

    return $app['twig']->render('teatro/categoria.html.twig', array(
        'cats' => $cats,
        'teatros' => $teatros,
    ));
})
->bind('teatro')
->value('id', 1)
->value('slug', null)
->assert('locale', $app['config.locales.regexp'])
->assert('section', 'teatro|teatre|theater');

/**
 * Renders design category
 */
$app->get('/{locale}/{section}', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})
->bind('disenyo')
->assert('locale', $app['config.locales.regexp'])
->assert('section', 'diseño|disseny|design');

/**
 * Renders restoration category
 */
$app->get('/{locale}/{section}', function (Silex\Application $app) {
    return $app['twig']->render('static/home.html.twig');
})
->bind('restauracion')
->assert('locale', $app['config.locales.regexp'])
->assert('section', 'restauración|restoration|restauració');