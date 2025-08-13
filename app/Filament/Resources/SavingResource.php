<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Saving;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SavingResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Tuxones\JsMoneyField\Forms\Components\JSMoneyInput;
use App\Filament\Resources\SavingResource\RelationManagers;

class SavingResource extends Resource
{
    protected static ?string $model = Saving::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(Auth::user()->id),

                Select::make('wallet_id')
                    ->relationship('wallet', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Wallet'),

                TextInput::make('name')
                    ->required()
                    ->label('Saving Name'),

                Select::make('type')
                    ->label('Type')
                    ->searchable()
                    ->required()
                    ->reactive() // wajib biar bisa trigger perubahan
                    ->options([
                        'gold' => 'gold',
                        'money' => 'money',
                    ]),

                // Amount akan muncul sesuai type
                Forms\Components\Fieldset::make('Amount Input')
                    ->schema([
                        JSMoneyInput::make('amount')
                            ->label('Amount')
                            ->prefix('Rp')
                            ->currency('IDR')
                            ->locale('id-ID')
                            ->default(0)
                            ->required()
                            ->visible(fn(callable $get) => $get('type') === 'money')
                            ->formatStateUsing(function ($state) {
                                if (is_null($state) || $state === '') {
                                    return 0.00;
                                }
                                return number_format(
                                    floatval(str_replace(['.', ','], ['', '.'], $state)),
                                    2,
                                    '.',
                                    ''
                                );
                            }),

                        TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->label('grams')
                            ->visible(fn(callable $get) => $get('type') === 'gold'),
                    ]),


                Textarea::make('description')
                    ->autosize(),

                DatePicker::make('saving_date')
                    ->date()
                    ->label('Date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Saving Name'),

                Tables\Columns\TextColumn::make('wallet.name')
                    ->label('Wallet'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->type === 'money') {
                            return 'IDR ' . number_format($state, 2, ',', '.');
                        } elseif ($record->type === 'gold') {
                            return $state . ' gram';
                        }
                        return $state;
                    })
                    ->color(function ($record) {
                        return match ($record->type) {
                            'money' => 'success', // hijau
                            'gold'  => 'warning', // kuning emas
                            default => null,
                        };
                    }),

                Tables\Columns\TextColumn::make('saving_date')
                    ->date()
                    ->label('Date'),
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
            'index' => Pages\ListSavings::route('/'),
            'create' => Pages\CreateSaving::route('/create'),
            'edit' => Pages\EditSaving::route('/{record}/edit'),
        ];
    }
}
