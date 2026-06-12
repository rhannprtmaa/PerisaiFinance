<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListTransactions
 * 
 * This class handles the listing of transactions in the Filament admin panel.
 */
class ListTransactions extends ListRecords
{
    /**
     * The resource associated with this page.
     *
     * @var string
     */
    protected static string $resource = TransactionResource::class;

    /**
     * Get the actions for the header.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}