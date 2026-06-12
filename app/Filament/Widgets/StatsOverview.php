<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Transaction;

/**
 * Class StatsOverview
 * * Widget class for displaying an enhanced statistical overview of transactions.
 */
class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    /**
     * @var Carbon|null
     */
    private ?Carbon $startDate;

    /**
     * @var Carbon|null
     */
    private ?Carbon $endDate;

    /**
     * Get the statistics to be displayed in the widget.
     *
     * @return array
     */
    protected function getStats(): array
    {
        $this->startDate = $this->parseDate($this->filters['startDate'] ?? null);
        $this->endDate = $this->parseDate($this->filters['endDate'] ?? now());

        // 1. Hitung total nilai finansial
        $pemasukan = $this->calculateTotal(Transaction::income(), $this->startDate, $this->endDate);
        $pengeluaran = $this->calculateTotal(Transaction::expenses(), $this->startDate, $this->endDate);
        $selisih = $pemasukan - $pengeluaran;

        // 2. Ambil data tren harian dinamis untuk grafik mini (Sparkline)
        $pemasukanTrend = $this->getTrendData(Transaction::income(), $this->startDate, $this->endDate);
        $pengeluaranTrend = $this->getTrendData(Transaction::expenses(), $this->startDate, $this->endDate);

        return [
            // Kartu Pemasukan (Tema Hijau / Success)
            Stat::make('Total Pemasukan', $this->formatCurrency($pemasukan))
                ->description('Log arus kas masuk')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($pemasukanTrend)
                ->color('success'),

            // Kartu Pengeluaran (Tema Merah / Danger)
            Stat::make('Total Pengeluaran', $this->formatCurrency($pengeluaran))
                ->description('Log arus kas keluar')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart($pengeluaranTrend)
                ->color('danger'),

            // Kartu Selisih Saldo Bersih (Dinamis Otomatis berubah sesuai kondisi keuangan)
            Stat::make('Selisih (Saldo Bersih)', $this->formatCurrency($selisih))
                ->description($selisih >= 0 ? 'Kondisi finansial Surplus' : 'Kondisi finansial Defisit')
                ->descriptionIcon($selisih >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->chart($this->getSplitTrend($pemasukanTrend, $pengeluaranTrend))
                ->color($selisih >= 0 ? 'success' : 'danger'),
        ];
    }

    /**
     * Parse a date string into a Carbon instance.
     *
     * @param string|null $date
     * @return Carbon|null
     */
    private function parseDate(?string $date): ?Carbon
    {
        return $date ? Carbon::parse($date) : null;
    }

    /**
     * Calculate the total amount for a given query within a date range.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return float
     */
    private function calculateTotal($query, ?Carbon $startDate, ?Carbon $endDate): float
    {
        return $query->clone()
                     ->when($startDate, fn($q) => $q->where('date_transaction', '>=', $startDate))
                     ->when($endDate, fn($q) => $q->where('date_transaction', '<=', $endDate))
                     ->sum('amount');
    }

    /**
     * Get dynamic daily trend data for the mini sparkline charts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    private function getTrendData($query, ?Carbon $startDate, ?Carbon $endDate): array
    {
        $trend = $query->clone()
            ->when($startDate, fn($q) => $q->where('date_transaction', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date_transaction', '<=', $endDate))
            ->groupBy('date_transaction')
            ->orderBy('date_transaction', 'asc')
            ->selectRaw('sum(amount) as total')
            ->pluck('total')
            ->map(fn($value) => (float) $value)
            ->toArray();

        // Jika data kosong, berikan nilai default flat [0, 0] agar grafik Filament tidak patah
        return empty($trend) ? [0, 0] : $trend;
    }

    /**
     * Helper to map a rough dynamic line for net balance widget.
     */
    private function getSplitTrend(array $income, array $expense): array
    {
        $trend = [];
        $max = max(count($income), count($expense));
        for ($i = 0; $i < $max; $i++) {
            $inc = $income[$i] ?? 0;
            $exp = $expense[$i] ?? 0;
            $trend[] = $inc - $exp;
        }
        return empty($trend) ? [0, 0] : $trend;
    }

    /**
     * Format a number as a currency string.
     *
     * @param float $amount
     * @return string
     */
    private function formatCurrency(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.'); // Mengubah ke format tanpa desimal sen agar lebih rapi di kartu
    }
}
