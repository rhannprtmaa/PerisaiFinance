<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString; // Mengimpor komponen pembaca HTML langsung

/**
 * Class CategoryResource
 * * Resource class for managing categories in the Filament admin panel.
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

            // 1. Menggunakan Hidden Field untuk mengamankan data & validasi ke database
            Forms\Components\Hidden::make('image')
                ->required(),

            // 2. Menggunakan Placeholder dengan isi INLINE HTML & ALPINe.JS (Bebas dari Error File Not Found!)
            Forms\Components\Placeholder::make('emoji_picker_wrapper')
                ->label('Pilih Emoticon Kategori')
                ->content(fn () => new HtmlString('
                    <div x-data="{
                        open: false,
                        state: $wire.entangle(\'data.image\')
                    }" class="relative">

                        <button
                            type="button"
                            @click="open = !open"
                            class="w-full flex items-center justify-between gap-3 px-3 py-2 text-left bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                            style="border-radius: 0.5rem; border-width: 1px; min-height: 42px;"
                        >
                            <div class="flex items-center gap-3">
                                <span x-text="state || \'😀\'" class="text-2xl"></span>
                                <span x-text="state ? \'Emoticon Terpilih\' : \'Klik untuk membuka keyboard emoji...\'" class="text-sm text-gray-500 dark:text-gray-400"></span>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-transition
                            class="absolute left-0 z-50 mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl dark:bg-gray-900 dark:border-gray-800 p-2"
                            style="display: none; min-width: 320px; max-width: 350px;"
                        >
                            <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@1/index.js"></script>
                            <style>
                                emoji-picker {
                                    --border-color: transparent;
                                    --background: transparent;
                                    width: 100%;
                                    height: 300px;
                                }
                            </style>
                            <emoji-picker
                                @emoji-click="state = $event.detail.unicode; open = false"
                                class="light dark:dark"
                            ></emoji-picker>
                        </div>
                    </div>
                ')),
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
            // Menampilkan karakter emoji ukuran besar di tabel utama
            Tables\Columns\TextColumn::make('image')
                ->label('Emoticon')
                ->extraAttributes([
                    'class' => 'text-2xl font-normal py-1'
                ]),

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
