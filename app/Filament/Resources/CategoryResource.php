<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Class CategoryResource
 * 
 * Resource class for managing categories in the Filament admin panel.
 */
class CategoryResource extends Resource
{
    /**
     * The model associated with the resource.
     *
     * @var string|null
     */
    protected static ?string $model = Category::class;

    /**
     * The icon used for navigation.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-c-list-bullet';

    /**
     * Define the form schema for the resource.
     *
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->label('Category Name'),

            Forms\Components\Toggle::make('is_expense')
                ->required()
                ->label('Is Expense'),

            Forms\Components\FileUpload::make('image')
                ->image()
                ->required()
                ->label('Category Image'),
        ]);
    }

    /**
     * Define the table schema for the resource.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('image')
                ->label('Image'),

            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->label('Name'),

            Tables\Columns\IconColumn::make('is_expense')
                ->label('Type')
                ->trueIcon('heroicon-o-arrow-up-circle')
                ->falseIcon('heroicon-o-arrow-down-circle')
                ->trueColor('danger')
                ->falseColor('success')
                ->boolean(),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Created At'),

            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Updated At'),

            Tables\Columns\TextColumn::make('deleted_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Deleted At'),
        ])
        ->filters([
            // Define filters here
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    /**
     * Define the relations for the resource.
     *
     * @return array
     */
    public static function getRelations(): array
    {
        return [
            // Define relations here
        ];
    }

    /**
     * Define the pages for the resource.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}