<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Wallet;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\WalletResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\WalletResource\RelationManagers;
use Tuxones\JsMoneyField\Forms\Components\JSMoneyInput;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Wallet Name'),

                Forms\Components\Select::make('type')
                    ->options([
                        'cash' => 'Cash',
                        'bank' => 'Bank',
                        'e-wallet' => 'E-Wallet',
                    ])
                    ->required(),

                JSMoneyInput::make('admin_fee')
                    ->label('Admin Fee')
                    ->prefix('Rp')
                    ->currency('IDR') // ISO 4217 Currency Code, example: USD
                    ->locale('id-ID')
                    ->default(0) // nilai default jika kosong
                    ->dehydrateStateUsing(fn($state) => $state ?? 0) // pastikan tetap 0 saat disimpan jika null
                    ->required(false), // BCP 47 Locale Code, example: en-US

                JSMoneyInput::make('balance')
                    ->label('Balance')
                    ->prefix('Rp')
                    ->currency('IDR') // ISO 4217 Currency Code, example: USD
                    ->locale('id-ID'), // BCP 47 Locale Code, example: en-US


                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->autosize()
                    ->required(),

                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::user()->id),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Wallet Name'),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable()
                    ->label('Type'),
                Tables\Columns\TextColumn::make('currency')
                    ->formatStateUsing(fn($state) => (float) ($state ?? 0))
                    ->money('IDR')
                    ->sortable()
                    ->label('Remaining Balance'),
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
            'index' => Pages\ListWallets::route('/'),
            'create' => Pages\CreateWallet::route('/create'),
            'edit' => Pages\EditWallet::route('/{record}/edit'),
        ];
    }
}
