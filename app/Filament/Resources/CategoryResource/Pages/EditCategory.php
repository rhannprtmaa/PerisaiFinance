<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditCategory
 * 
 * This class handles the editing of categories in the Filament admin panel.
 */
class EditCategory extends EditRecord
{
    /**
     * The resource associated with this page.
     *
     * @var string
     */
    protected static string $resource = CategoryResource::class;

    /**
     * Get the header actions for the edit page.
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