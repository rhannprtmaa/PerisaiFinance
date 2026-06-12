<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    /**
     * LOGIKA UTAMA: Membatasi tampilan data berdasarkan Role yang sedang Login
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Jika user yang login BUKAN bendahara, batasi hanya melihat departemennya sendiri
        if (auth()->check() && auth()->user()->role !== 'bendahara') {
            $query->where('department', auth()->user()->department);
        }

        return $query;
    }

    /**
     * Blueprint Formulir Input Transaksi
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->label('Nama Transaksi / Penanggung Jawab'),

            // Kolom Departemen Cerdas
                    Forms\Components\Select::make('department')
                ->label('Departemen / Divisi')
                ->options([
                    'bendahara' => '💰 Internal Bendahara',
                    'penalaran' => '🧠 Divisi Penalaran', // Tambahkan baris baru ini!
                    'hrd' => '👥 HRD (Human Resources)',
                    'it' => '💻 IT & Technology',
                    'marketing' => '📢 Marketing & Pemasaran',
                ])
                ->searchable()
                ->preload()
                ->default(fn () => auth()->user()->department) // Otomatis terisi sesuai divisi user yang login
                ->disabled(fn () => auth()->user()->role !== 'bendahara') // Kunci input jika bukan bendahara
                ->dehydrated() // Tetap simpan ke database meskipun statusnya disabled
                ->required(),

            Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->required()
                ->searchable()
                ->preload()
                ->label('Kategori'),

            Forms\Components\DatePicker::make('date_transaction')
                ->required()
                ->label('Tanggal Transaksi'),

            Forms\Components\TextInput::make('amount')
                ->numeric()
                ->prefix('Rp')
                ->required()
                ->label('Jumlah (Nominal)'),

            Forms\Components\TextInput::make('note')
                ->required()
                ->label('Catatan ringkas'),

            // FIX: Gambar diubah menjadi Opsional (Nullable)
            Forms\Components\FileUpload::make('image')
                ->image()
                ->label('Foto Nota / Bukti Transaksi (Opsional)')
                ->nullable(),
        ]);
    }

    /**
     * Blueprint Tabel Riwayat Transaksi
     */
    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('date_transaction')
                ->date('d M Y')
                ->sortable()
                ->label('Tanggal'),

            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->label('Nama / PJB'),

            // Menampilkan badge departemen dengan warna elegan
                    Tables\Columns\TextColumn::make('department')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'bendahara' => 'success',
                    'penalaran' => 'warning', // Tambahkan ini agar divisi penalaran berwarna Oranye/Amber
                    'it' => 'info',
                    'hrd' => 'warning',
                    'marketing' => 'purple',
                    default => 'gray',
                })
                ->formatStateUsing(fn (string $state) => strtoupper($state))
                ->label('Divisi'),

            // Menampilkan emoticon kategori + namanya jika relasi di model aman
            Tables\Columns\TextColumn::make('category.name')
                ->label('Kategori'),

            // Format Rupiah rapi tanpa desimal sen
            Tables\Columns\TextColumn::make('amount')
                ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ->sortable()
                ->label('Nominal'),

            Tables\Columns\TextColumn::make('note')
                ->limit(30)
                ->label('Catatan'),

            Tables\Columns\ImageColumn::make('image')
                ->label('Nota'),
        ])
        ->filters([
            // Filter Berdasarkan Departemen (Sangat berguna untuk akun Bendahara)
            Tables\Filters\SelectFilter::make('department')
                ->options([
                    'bendahara' => 'Internal Bendahara',
                    'hrd' => 'HRD',
                    'it' => 'IT',
                    'marketing' => 'Marketing',
                    'operations' => 'Operasional',
                ])
                ->label('Filter Divisi'),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
