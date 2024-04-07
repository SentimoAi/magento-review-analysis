<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Cron;

use Sentimo\ReviewAnalysis\Model\Command\SyncReviewsCommand;

class SyncReviews
{
    public function __construct(
        private readonly SyncReviewsCommand $syncReviewsCommand
    ) {
    }

    public function execute(): void
    {
        $this->syncReviewsCommand->execute();
    }
}
