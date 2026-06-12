<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateTransaction
 * 
 * This class handles the creation of transactions in the Filament admin panel.
 */
class CreateTransaction extends CreateRecord
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