<?php

declare(strict_types=1);

namespace App\UseCases\StatisticPerYear;

use App\Models\StatisticPerYear;
use App\UseCases\Statistic\ArrangeStatisticPerMY;
use App\UseCases\Statistic\CheckStatisticStatus;

class GetYearlyStatisticByYear
{
    public function __construct(
        private CheckStatisticStatus $checkStatisticStatus,
        private ArrangeStatisticPerMY $arrangeStatistic,
    ) {
    }

    /**
     * 指定された年の統計データを整えて返す.
     */
    public function invoke(int $year): array
    {
        $statisticPerYear = StatisticPerYear::where('year', $year)->first();
        $statisticStatus = $this->checkStatisticStatus->invoke($statisticPerYear);
        $statisticPerYearProceed = $this->arrangeStatistic->invoke($statisticPerYear, $statisticStatus);
        if (null === $statisticPerYear) {
            return [];
        }

        return $statisticPerYearProceed->toArray();
    }
}
