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

    /**
     * @param $path
     * @return Post
     * @throws \Exception
     */
    private function readFileIntoPost($path)
    {
        $content = file_get_contents($path);
        $tarray = explode(PHP_EOL, $content);
        $data = array();

        foreach ($tarray as $element) {
            $split = explode(':', $element);

            if (trim($split[0]) == 'title') {
                $data['title'] = trim($split[1]);
            } else {
                if (trim($split[0]) == 'date') {
                    $data['date'] = trim($split[1]);
                } else {
                    if (trim($split[0]) == 'time') {
                        $data['time'] = trim($split[1]);
                    }
                }
            }

            $data['text'] = trim($tarray[5]);
        }

        if (count($data) == 0) {
            throw new \Exception('Misformed Post file!');
        }

        $post = new Post();
        $post->setTitle($data['title'])
            ->setText($data['text'])
            ->setRawContent($content)
            ->setDate(new \DateTime($data['date']));

        var_dump($post);

        return $post;
    }

    public function getAll()
    {
        $posts = array();

        try {
            $this->finder->files()->in($this->folder);

            foreach ($this->finder as $file) {
                $filename = $file->getRelativePathname();

                $posts[] = $this->readFileIntoPost($file->getRealpath());
            }
        } catch (\Exception $exc) {
            var_dump($exc);
        }

        return $posts;
    }
}
