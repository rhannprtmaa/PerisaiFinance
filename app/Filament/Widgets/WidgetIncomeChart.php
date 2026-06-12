<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Transaction;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

/**
 * Class WidgetIncomeChart
 * 
 * Widget class for displaying income trends in a chart.
 */
class WidgetIncomeChart extends ChartWidget
{
    use InteractsWithPageFilters;

    /**
     * The heading of the widget.
     *
     * @var string|null
     */
    protected static ?string $heading = 'Penghasilan';

    /**
     * The color of the widget.
     *
     * @var string
     */
    protected static string $color = 'success';

    /**
     * Get the data for the chart.
     *
     * @return array
     */
    protected function getData(): array
    {
        $startDate = $this->parseDate($this->filters['startDate'] ?? null, now()->startOfYear());
        $endDate = $this->parseDate($this->filters['endDate'] ?? null, now());

        $data = $this->getIncomeData($startDate, $endDate);

        $backgroundColor = 'rgba(75, 192, 192, 0.2)'; // Green
        $borderColor = 'rgba(75, 192, 192, 1)'; // Green

        return [
            'datasets' => [
                [
                    'label' => 'Penghasilan Per Hari',
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
        return 'bar';
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
     * Get the income data within a date range.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    private function getIncomeData(Carbon $startDate, Carbon $endDate)
    {
        return Trend::query(Transaction::income()->newQuery())
            ->between(start: $startDate, end: $endDate)
            ->perDay()
            ->sum('amount')
            ->sortBy('date');
    }
}