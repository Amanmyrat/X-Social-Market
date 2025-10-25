<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyLoginRewardResource\Pages;
use App\Models\DailyLoginReward;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DailyLoginRewardResource extends Resource
{
    protected static ?string $model = DailyLoginReward::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationLabel = 'Daily Login Streaks';

    protected static ?string $navigationGroup = 'TNT Coin System';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'username')
                    ->searchable()
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('current_streak')
                    ->numeric()
                    ->disabled(),
                Forms\Components\TextInput::make('highest_streak')
                    ->numeric()
                    ->disabled(),
                Forms\Components\DatePicker::make('last_login_date')
                    ->disabled(),
                Forms\Components\TextInput::make('total_earned')
                    ->numeric()
                    ->disabled()
                    ->suffix(' TNT'),
                Forms\Components\TextInput::make('total_claims')
                    ->numeric()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.username')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_streak')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 7 => 'success',
                        $state >= 4 => 'warning',
                        default => 'gray',
                    })
                    ->suffix(' days'),
                Tables\Columns\TextColumn::make('highest_streak')
                    ->sortable()
                    ->suffix(' days'),
                Tables\Columns\TextColumn::make('last_login_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_earned')
                    ->money('USD', divideBy: 1)
                    ->sortable()
                    ->suffix(' TNT'),
                Tables\Columns\TextColumn::make('total_claims')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('active_streak')
                    ->label('Active Streaks')
                    ->query(fn ($query) => $query->where('current_streak', '>', 0)),
                Tables\Filters\Filter::make('high_streaks')
                    ->label('High Streaks (5+)')
                    ->query(fn ($query) => $query->where('current_streak', '>=', 5)),
                Tables\Filters\Filter::make('recent_activity')
                    ->label('Active Last 7 Days')
                    ->query(fn ($query) => $query->whereDate('last_login_date', '>=', now()->subDays(7))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('current_streak', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailyLoginRewards::route('/'),
            'view' => Pages\ViewDailyLoginReward::route('/{record}'),
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

