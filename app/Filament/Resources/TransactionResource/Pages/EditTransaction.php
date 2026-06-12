<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditTransaction
 * 
 * This class handles the editing of transactions in the Filament admin panel.
 */
class EditTransaction extends EditRecord
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
            DeleteAction::make(),
        ];
    }
}