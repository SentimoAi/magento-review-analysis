<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\RequestParam;

interface ReviewGetRequestParamBuilderInterface
{
    /**
     * @param array<int> $reviewIds
     * @return array<string,int|string|int[]|string[]>
     */
    public function buildRequestParam(array $reviewIds): array;
}
