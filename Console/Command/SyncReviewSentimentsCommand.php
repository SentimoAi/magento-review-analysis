<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Console\Command;

use Sentimo\ReviewAnalysis\Model\Command\PostReviewsCommand as PostReviews;
use Sentimo\ReviewAnalysis\Model\Command\SyncReviewSentimentsCommand as SyncReviewSentiments;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncReviewSentimentsCommand extends Command
{
    public function __construct(private readonly SyncReviewSentiments $syncReviewSentimentsCommand)
    {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('sentimo:review-analysis:sync-reviews');
        $this->setDescription('Sync review sentiments from Sentimo');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->syncReviewSentimentsCommand->execute();
    }
}
