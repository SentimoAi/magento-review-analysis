<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Cron;

use Sentimo\ReviewAnalysis\Model\Command\PostReviewsCommand;

class PostReviews
{
    public function __construct(
        private readonly PostReviewsCommand $postReviewsCommand
    ) {
    }

    public function execute(): void
    {
        $this->postReviewsCommand->execute();
    }
}
