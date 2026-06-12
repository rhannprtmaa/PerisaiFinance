<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    /**
     * Define the filters form schema for the dashboard.
     *
     * @param Form $form
     * @return Form
     */
    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    DatePicker::make('startDate')
                        ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                        ->label('Start Date'),
                    DatePicker::make('endDate')
                        ->minDate(fn (Get $get) => $get('startDate') ?: now())
                        ->maxDate(now())
                        ->label('End Date'),
                ])
                ->columns(2),
        ]);
    }
}