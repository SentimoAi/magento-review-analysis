<?php

declare(strict_types=1);

namespace CustomerFeel\ApiConnector\Console\Command;

use CustomerFeel\ApiConnector\Model\Command\PostReviewsCommand as PostReviews;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostReviewsCommand extends Command
{
    public function __construct(private readonly PostReviews $postReviewsCommand)
    {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('customerfeel:post-reviews');
        $this->setDescription('Post reviews to CustomerFeel');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->postReviewsCommand->execute();
    }
}
