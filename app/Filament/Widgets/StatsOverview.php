<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Transaction;

/**
 * Class StatsOverview
 * 
 * Widget class for displaying statistical overview of transactions.
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

        $pemasukan = $this->calculateTotal(Transaction::income(), $this->startDate, $this->endDate);
        $pengeluaran = $this->calculateTotal(Transaction::expenses(), $this->startDate, $this->endDate);

        return [
            Stat::make('Total Pemasukan', $this->formatCurrency($pemasukan)),
            Stat::make('Total Pengeluaran', $this->formatCurrency($pengeluaran)),
            Stat::make('Selisih', $this->formatCurrency($pemasukan - $pengeluaran)),
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
        return $query->when($startDate, fn($q) => $q->where('date_transaction', '>=', $startDate))
                     ->when($endDate, fn($q) => $q->where('date_transaction', '<=', $endDate))
                     ->sum('amount');
    }

    /**
     * Format a number as a currency string.
     *
     * @param float $amount
     * @return string
     */
    private function formatCurrency(float $amount): string
    {
        return 'Rp ' . number_format($amount, 2, ',', '.');
    }
}