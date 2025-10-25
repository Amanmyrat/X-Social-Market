<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DailyLoginStatsWidget;
use App\Models\AppSetting;
use App\Services\DailyLoginService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DailyLoginSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.daily-login-settings';

    protected static ?string $navigationLabel = 'Daily Login Settings';

    protected static ?string $navigationGroup = 'TNT Coin System';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'base_reward' => AppSetting::get('daily_login_base_reward', 2),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Daily Login Reward Configuration')
                    ->description('Configure the base reward amount for daily login streaks')
                    ->schema([
                        Forms\Components\TextInput::make('base_reward')
                            ->label('Base Reward (TNT)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(1000)
                            ->step(0.01)
                            ->suffix('TNT')
                            ->helperText('Users will receive: Base Reward × Current Streak Day (1-7)')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        AppSetting::set(
            'daily_login_base_reward',
            $data['base_reward'],
            'decimal',
            'Base reward for daily login (TNT coins)'
        );

        Notification::make()
            ->success()
            ->title('Settings saved')
            ->body('Daily login base reward has been updated successfully.')
            ->send();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DailyLoginStatsWidget::class,
        ];
    }
}

