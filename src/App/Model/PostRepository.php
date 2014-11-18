<?php

namespace App\Model;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Parsedown;

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

    /**
     * @var Parsedown
     */
    private $parsedown;

    public function __construct()
    {
        $this->finder = new Finder();
        $this->folder = __DIR__ . '/../../../posts';
        $this->parsedown = new Parsedown();
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

        //prepare array
        unset($tarray[0]);
        unset($tarray[5]);
        $tarray = array_values($tarray);

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
                    } else {
                        if (trim($split[0]) == 'slug') {
                            $data['slug'] = trim($split[1]);
                        }
                    }
                }
            }

            $tmp = array_slice($tarray, 4);

            $data['text'] = implode(PHP_EOL, $tmp);
        }

        if (count($data) == 0) {
            throw new \Exception('Misformed Post file!');
        }

        $d = explode('-', $data['time']);
        $h = $d[0];
        $i = $d[1];
        $s = $d[2];

        $post = new Post();
        $post->setTitle($data['title'])
            ->setText($this->parsedown->text($data['text']))
            ->setSlug($data['slug'])
            ->setRawContent($content)
            ->setDate((new \DateTime($data['date']))->setTime($h, $i, $s));

        return $post;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $posts = array();

        try {
            $this->finder->files()->in($this->folder);
            $this->finder->sort(
                function ($a, $b) {
                    return strcmp($b->getRealpath(), $a->getRealpath());
                }
            );

            foreach ($this->finder as $file) {
                $filename = $file->getRelativePathname();

                $posts[] = $this->readFileIntoPost($file->getRealpath());
            }
        } catch (\Exception $exc) {
            var_dump($exc);
        }

        return $posts;
    }

    /**
     * @param $slug
     * @return Post
     * @throws \Exception
     */
    public function getOneBySlug($slug)
    {
        $this->finder->files()->in($this->folder)->name('*' . $slug . '.md');

        foreach ($this->finder as $file) {
            return $this->readFileIntoPost($file->getRealpath());
        }

        throw new FileNotFoundException($slug);
    }
}
