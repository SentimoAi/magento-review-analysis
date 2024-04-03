<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Console\Command;

use Sentimo\ReviewAnalysis\Model\Command\PostReviewsCommand as PostReviews;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostReviewsCommand extends Command
{
    public function __construct(
        private readonly PostReviews $postReviewsCommand
    ) {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('sentimo:review-analysis:post-reviews');
        $this->setDescription('Post reviews to Sentimo');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->postReviewsCommand->execute();

        return self::SUCCESS;
    }
}
