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
 * * Widget class for displaying expense trends in a chart.
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
     * Mengatur ukuran widget mengambil 1 kolom (setengah lebar layar)
     * agar bisa bersandingan kiri-kanan secara simetris dengan WidgetIncomeChart.
     */
    protected int | string | array $columnSpan = 1;

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

        // Menggunakan kombinasi warna Tailwind CSS (Emerald & Red) yang lebih soft
        $backgroundColor = $isIncreasing ? 'rgba(239, 68, 68, 0.1)' : 'rgba(34, 197, 94, 0.1)';
        $borderColor = $isIncreasing ? 'rgba(239, 68, 68, 1)' : 'rgba(34, 197, 94, 1)';

        return [
            'datasets' => [
                [
                    'label' => 'Pengeluaran Per Hari',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $borderColor,
                    'borderWidth' => 2,
                    'tension' => 0.35, // Membuat garis melengkung bergelombang secara halus (smooth curve)
                    'fill' => true,    // Memberikan efek bayangan warna gradasi di bawah garis grafik
                ],
            ],
            // Mengubah format ke 'd M' agar serasi dengan grafik penghasilan
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('d M')),
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
        // FIX: Menyaring rute grafik garis pengeluaran agar otomatis terkunci sesuai divisi yang login selain bendahara
        $query = Transaction::expenses()->newQuery()
            ->when(auth()->check() && auth()->user()->role !== 'bendahara', fn($q) => $q->where('department', auth()->user()->department));

        return Trend::query($query)
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
