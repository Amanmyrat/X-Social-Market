<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferralResource\Pages;
use App\Models\Referral;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReferralResource extends Resource
{
    protected static ?string $model = Referral::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Referrals';

    protected static ?string $navigationGroup = 'TNT Coin System';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('referrer_id')
                    ->relationship('referrer', 'username')
                    ->searchable()
                    ->required()
                    ->disabled(),
                Forms\Components\Select::make('referee_id')
                    ->relationship('referee', 'username')
                    ->searchable()
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('reward_amount')
                    ->numeric()
                    ->disabled()
                    ->suffix(' TNT'),
                Forms\Components\Toggle::make('reward_claimed')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('reward_claimed_at')
                    ->disabled(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('referrer.username')
                    ->label('Referrer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('referee.username')
                    ->label('Referee')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reward_amount')
                    ->money('USD', divideBy: 1)
                    ->suffix(' TNT')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\IconColumn::make('reward_claimed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Referred At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reward_claimed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('reward_claimed')
                    ->label('Rewarded')
                    ->query(fn ($query) => $query->where('reward_claimed', true)),
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
            'index' => Pages\ListReferrals::route('/'),
            'view' => Pages\ViewReferral::route('/{record}'),
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

