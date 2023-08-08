<?php

declare(strict_types=1);

namespace CustomerFeel\ApiConnector\Console\Command;

use CustomerFeel\ApiConnector\Model\Command\PostReviewsCommand as PostReviews;
use CustomerFeel\ApiConnector\Model\Command\SyncReviewSentimentsCommand as SyncReviewSentiments;
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
        $this->setName('customerfeel:sync-reviews');
        $this->setDescription('Sync review sentiments from CustomerFeel');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->syncReviewSentimentsCommand->execute();
    }
}
