<?php

namespace App\Model;

use Symfony\Component\Finder\Finder;

class PostRepository
{
    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var string
     */
    private $folder;

    public function __construct()
    {
        $this->finder = new Finder();
        $this->folder = __DIR__ . '/../../../posts';
    }

    public function getAll()
    {
        $posts = array();

        try {
            $this->finder->files()->in($this->folder);

            foreach ($this->finder as $file) {
                $filename = $file->getRelativePathname();

                $content = file_get_contents($file->getRealpath());
                $datetime = new \DateTime();

                $post = new Post();
                $post->setTitle('')
                    ->setText('')
                    ->setRawContent($content)
                    ->setDate(null);

                $posts[] = $post;
            }
        } catch (\Exception $exc) {
            var_dump($exc);
        }

        return $posts;
    }
}
