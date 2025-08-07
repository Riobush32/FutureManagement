<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
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

                Forms\Components\Select::make('budget_id')
                    ->relationship('budget', 'name')
                    ->required()
                    ->label('From Budget'),

                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label('Category'),

                Forms\Components\Select::make('type')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'finish' => 'Finish',
                    ])
                    ->default('active')
                    ->required(),

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
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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
