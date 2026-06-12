<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateCategory
 * 
 * This class handles the creation of categories in the Filament admin panel.
 */
class CreateCategory extends CreateRecord
{
    /**
     * The resource associated with this page.
     *
     * @var string
     */
    protected static string $resource = CategoryResource::class;

    /**
     * Get the header actions for the create page.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}