<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ReferralStatsWidget;
use App\Models\AppSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ReferralSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static string $view = 'filament.pages.referral-settings';

    protected static ?string $navigationLabel = 'Referral Settings';

    protected static ?string $navigationGroup = 'TNT Coin System';

    protected static ?int $navigationSort = 5;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'referral_reward' => AppSetting::get('referral_reward', 10),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Referral Reward Configuration')
                    ->description('Configure the reward amount for successful referrals')
                    ->schema([
                        Forms\Components\TextInput::make('referral_reward')
                            ->label('Referral Reward (TNT)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(1000)
                            ->step(0.01)
                            ->suffix('TNT')
                            ->helperText('Reward given to referrer when someone signs up using their code')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        AppSetting::set(
            'referral_reward',
            $data['referral_reward'],
            'decimal',
            'Reward amount for successful referral (TNT coins)'
        );

        Notification::make()
            ->success()
            ->title('Settings saved')
            ->body('Referral reward has been updated successfully.')
            ->send();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ReferralStatsWidget::class,
        ];
    }
}

