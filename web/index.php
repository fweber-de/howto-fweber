<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Finder\Finder;

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->get('/', function () use ($app) {
    
    $posts = null;
    
    $finder = new Finder();
    $finder->files()->in(__DIR__ . '/posts');

    foreach ($finder as $file) {
        /*// Print the absolute path
        print $file->getRealpath()."\n";

        // Print the relative path to the file, omitting the filename
        print $file->getRelativePath()."\n";

        // Print the relative path to the file
        print $file->getRelativePathname()."\n";*/
        
        $posts[] = $file->getRealpath();
    }
    
    return $app['twig']->render('index.html.twig', array(
        'posts' => $posts
    ));
})->bind('root');

$app['debug'] = false;

$app->run();
