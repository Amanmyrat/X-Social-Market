<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'TNT Transactions';

    protected static ?string $navigationGroup = 'TNT Coin System';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'username')
                    ->searchable()
                    ->required()
                    ->disabled(),
                Forms\Components\Select::make('type')
                    ->options([
                        'earn' => 'Earn',
                        'spend' => 'Spend',
                        'refund' => 'Refund',
                        'admin_adjust' => 'Admin Adjustment',
                    ])
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('source')
                    ->disabled(),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('balance_before')
                    ->numeric()
                    ->disabled(),
                Forms\Components\TextInput::make('balance_after')
                    ->numeric()
                    ->disabled(),
                Forms\Components\Textarea::make('description')
                    ->disabled(),
                Forms\Components\KeyValue::make('metadata')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.username')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'success' => 'earn',
                        'danger' => 'spend',
                        'warning' => 'refund',
                        'primary' => 'admin_adjust',
                    ]),
                Tables\Columns\TextColumn::make('source')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('USD', divideBy: 1)
                    ->sortable()
                    ->suffix(' TNT'),
                Tables\Columns\TextColumn::make('balance_after')
                    ->label('New Balance')
                    ->money('USD', divideBy: 1)
                    ->suffix(' TNT'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(30),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'earn' => 'Earn',
                        'spend' => 'Spend',
                        'refund' => 'Refund',
                        'admin_adjust' => 'Admin Adjustment',
                    ]),
                Tables\Filters\SelectFilter::make('source')
                    ->options([
                        'daily_login' => 'Daily Login',
                        'purchase' => 'Purchase',
                        'referral' => 'Referral',
                        'admin' => 'Admin',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'view' => Pages\ViewTransaction::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}

