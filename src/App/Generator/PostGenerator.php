<?php

namespace App\Generator;

use App\Model\Post;

class PostGenerator
{
    /**
     * Generates slug
     *
     * @param $text
     * @return mixed|string
     */
    private function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * Generate Post
     *
     * @param $title
     * @return Post
     */
    public function generate($title)
    {
        $folder = __DIR__ . '/../../../posts';
        $filenamePattern = '[DATETIME]_[SLUG].md';

        $dt = new \DateTime('now');
        $datetime = $dt->format('Y-m-d_G-i');
        $slug = $this->slugify($title);

        $filename = str_replace('[DATETIME]', $datetime, str_replace('[SLUG]', $slug, $filenamePattern));

        $content = '---' . PHP_EOL;
        $content .= 'title: ' . $title . PHP_EOL;
        $content .= 'slug: ' . $slug . PHP_EOL;
        $content .= 'date: ' . $dt->format('Y-m-d') . PHP_EOL;
        $content .= 'time: ' . $dt->format('G-i-s') . PHP_EOL;
        $content .= '---' . PHP_EOL;

        file_put_contents($folder . '/' . $filename, $content);

        $post = new Post();
        $post->setDate($dt)
            ->setRawContent($content)
            ->setText(null)
            ->setTitle($title)
            ->setSlug($slug);

        return $post;
    }
}
