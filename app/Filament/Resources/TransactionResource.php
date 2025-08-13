<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransactionResource\Pages;
use Tuxones\JsMoneyField\Forms\Components\JSMoneyInput;
use App\Filament\Resources\TransactionResource\RelationManagers;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::user()->id),

                Forms\Components\Select::make('category_id')
                    ->relationship(
                        name: 'category',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn($query) => $query->select('id', 'name', 'type')
                    )
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} ({$record->type})")
                    ->required()
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->label('Category'),

                Forms\Components\TextInput::make('description')
                    ->required()
                    ->label('Description'),

                Forms\Components\DatePicker::make('transaction_date')
                    ->displayFormat('d/m/Y')
                    ->required(),

                JSMoneyInput::make('amount')
                    ->label('Amount')
                    ->prefix('Rp')
                    ->currency('IDR')
                    ->locale('id-ID')
                    ->default(0)
                    ->required()
                    ->formatStateUsing(function ($state) {
                        // Jika null atau kosong, set ke 0.00
                        if (is_null($state) || $state === '') {
                            return 0.00;
                        }

                        // Jika state adalah string (misalnya "500000"), ubah ke float dengan dua angka desimal
                        return number_format(floatval(str_replace(['.', ','], ['', '.'], $state)), 2, '.', '');
                    }),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->label('Details')
                    ->color(fn($record) => $record->category->type === 'expense' ? 'danger' : 'primary'),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->color(fn($record) => $record->category->type === 'expense' ? 'danger' : 'primary'),

                TextColumn::make('category.type')
                    ->label('Type')
                    ->color(fn($record) => $record->category->type === 'expense' ? 'danger' : 'primary'),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('IDR')
                    ->color(fn($record) => $record->category->type === 'expense' ? 'danger' : 'primary'),

                TextColumn::make('transaction_date')
                    ->label('Date')
                    ->color(fn($record) => $record->category->type === 'expense' ? 'danger' : 'primary'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn($query) => $query->with('category')->orderByDesc('transaction_date'));
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
