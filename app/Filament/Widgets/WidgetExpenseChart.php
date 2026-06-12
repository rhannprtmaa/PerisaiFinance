<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Transaction;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

/**
 * Class WidgetExpenseChart
 * 
 * Widget class for displaying expense trends in a chart.
 */
class WidgetExpenseChart extends ChartWidget
{
    use InteractsWithPageFilters;

    /**
     * The heading of the widget.
     *
     * @var string|null
     */
    protected static ?string $heading = 'Pengeluaran';

    /**
     * The color of the widget.
     *
     * @var string
     */
    protected static string $color = 'danger';

    /**
     * Get the data for the chart.
     *
     * @return array
     */
    protected function getData(): array
    {
        $startDate = $this->parseDate($this->filters['startDate'] ?? null, Carbon::now()->startOfMonth());
        $endDate = $this->parseDate($this->filters['endDate'] ?? null, now());

        $data = $this->getExpenseData($startDate, $endDate);

        $trendDirection = $data->map(fn (TrendValue $value) => $value->aggregate)->toArray();
        $isIncreasing = $this->isTrendIncreasing($trendDirection);

        $backgroundColor = $isIncreasing ? 'rgba(75, 192, 192, 0.2)' : 'rgba(255, 99, 132, 0.2)';
        $borderColor = $isIncreasing ? 'rgba(75, 192, 192, 1)' : 'rgba(255, 99, 132, 1)';

        return [
            'datasets' => [
                [
                    'label' => 'Pengeluaran Per Hari',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $borderColor,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('Y-m-d')),
        ];
    }

    /**
     * Get the type of the chart.
     *
     * @return string
     */
    protected function getType(): string
    {
        return 'line';
    }

    /**
     * Parse a date string into a Carbon instance or return a default value.
     *
     * @param string|null $date
     * @param Carbon $default
     * @return Carbon
     */
    private function parseDate(?string $date, Carbon $default): Carbon
    {
        return $date ? Carbon::parse($date) : $default;
    }

    /**
     * Get the expense data within a date range.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    private function getExpenseData(Carbon $startDate, Carbon $endDate)
    {
        return Trend::query(Transaction::expenses()->newQuery())
            ->between(start: $startDate, end: $endDate)
            ->perDay()
            ->sum('amount')
            ->sortBy('date');
    }

    /**
     * Determine if the trend is increasing.
     *
     * @param array $trendDirection
     * @return bool
     */
    private function isTrendIncreasing(array $trendDirection): bool
    {
        $sortedTrendDirection = $trendDirection;
        sort($sortedTrendDirection);
        return $trendDirection === $sortedTrendDirection;
    }
}