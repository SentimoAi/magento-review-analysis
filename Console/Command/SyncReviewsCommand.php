<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Console\Command;

use Sentimo\ReviewAnalysis\Model\Command\SyncReviewsCommand as SyncReviewsCommandModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncReviewsCommand extends Command
{
    public function __construct(
        private readonly SyncReviewsCommandModel $syncReviewsCommand
    ) {
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

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    //phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Syncing review sentiments from Sentimo...</info>');

        $this->syncReviewsCommand->execute();

        $output->writeln('<info>Review sentiments synced from Sentimo.</info>');

        return self::SUCCESS;
    }
}
