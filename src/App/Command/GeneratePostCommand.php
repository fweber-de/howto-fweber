<?php

namespace App\Command;

use App\Generator\PostGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratePostCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate:post')
            ->setDescription('Generate a Post file')
            ->addArgument(
                'title',
                InputArgument::REQUIRED,
                'The Post Title'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $generator = new PostGenerator();
        $post = $generator->generate($input->getArgument('title'));

        $output->writeln('The post was successfully created!');
    }
}
