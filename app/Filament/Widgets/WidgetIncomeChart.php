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
 * * Widget class for displaying income trends in a chart.
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
     * Mengatur ukuran widget mengambil 1 kolom (setengah lebar layar)
     * agar bisa bersandingan kiri-kanan secara simetris dengan WidgetExpenseChart.
     */
    protected int | string | array $columnSpan = 1;

    /**
     * Get the data for the chart.
     *
     * @return array
     */
    protected function getData(): array
    {
        // FIX: Disamakan menggunakan startOfMonth() agar rentang waktu bawaan seimbang dengan grafik pengeluaran
        $startDate = $this->parseDate($this->filters['startDate'] ?? null, Carbon::now()->startOfMonth());
        $endDate = $this->parseDate($this->filters['endDate'] ?? null, now());

        $data = $this->getIncomeData($startDate, $endDate);

        // Menggunakan warna hijau Emerald yang lebih modern dan transparan
        $backgroundColor = 'rgba(16, 185, 129, 0.15)';
        $borderColor = 'rgba(16, 185, 129, 1)';

        return [
            'datasets' => [
                [
                    'label' => 'Penghasilan Per Hari',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $borderColor,
                    'borderWidth' => 2,
                    'borderRadius' => 4, // Membuat ujung kotak diagram batang agak melengkung estetik
                ],
            ],
            // Mengubah format ke 'd M' (contoh: 12 Jun) agar serasi dengan grafik pengeluaran
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
        // FIX: Menyaring query diagram batang pendapatan agar terkunci per divisi saat login selain bendahara
        $query = Transaction::income()->newQuery()
            ->when(auth()->check() && auth()->user()->role !== 'bendahara', fn($q) => $q->where('department', auth()->user()->department));

        return Trend::query($query)
            ->between(start: $startDate, end: $endDate)
            ->perDay()
            ->sum('amount')
            ->sortBy('date');
    }
}
