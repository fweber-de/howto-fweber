<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

$app = new Silex\Application();

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    array(
        'twig.path' => __DIR__ . '/../views',
    )
);
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

//error handling
ErrorHandler::register();
ExceptionHandler::register();

$app->get(
    '/',
    function () use ($app) {

        $posts = (new \App\Model\PostRepository())->getAll();

        return $app['twig']->render(
            'index.html.twig',
            array(
                'posts' => $posts
            )
        );
    }
)->bind('root');

$app->get(
    '/post/{slug}',
    function ($slug) use ($app) {

        $post = (new \App\Model\PostRepository())->getOneBySlug($slug);

        return $app['twig']->render(
            'post.html.twig',
            array(
                'post' => $post
            )
        );
    }
)->bind('post');

$app['debug'] = false;

$app->run();
