<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

/**
 * Class ListCategories
 * 
 * This class handles the listing of categories in the Filament admin panel.
 */
class ListCategories extends ListRecords
{
    /**
     * The resource associated with this page.
     *
     * @var string
     */
    protected static string $resource = CategoryResource::class;

    /**
     * Get the header actions for the list page.
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