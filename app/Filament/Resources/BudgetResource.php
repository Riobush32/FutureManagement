<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Budget;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BudgetResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Tuxones\JsMoneyField\Forms\Components\JSMoneyInput;
use App\Filament\Resources\BudgetResource\RelationManagers;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationGroup = 'Finance Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::user()->id),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Budget Name'),

                Forms\Components\Select::make('wallet_id')
                    ->relationship('wallet', 'name')
                    ->required()
                    ->label('From wallet'),

                JSMoneyInput::make('amount')
                    ->label('Budget Amount')
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

                Forms\Components\DatePicker::make('start_date')
                    ->displayFormat('d/m/Y')
                    ->required(),

                Forms\Components\DatePicker::make('end_date')
                    ->displayFormat('d/m/Y')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'finish' => 'Finish',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Budget Name'),

                Tables\Columns\TextColumn::make('amount')
                    ->money('IDR')
                    ->label('Budget Amount'),

                Tables\Columns\TextColumn::make('used_budget')
                    ->label('Budget Used')
                    ->money('IDR'), // Jika ingin tampil sebagai uang

                Tables\Columns\TextColumn::make('remaining_budget')
                    ->label('Remaining Budget')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->label('Start Date'),

                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->label('End Date'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),
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
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
        ];
    }
}
