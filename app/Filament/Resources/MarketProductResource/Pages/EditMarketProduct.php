<?php

namespace App\Filament\Resources\MarketProductResource\Pages;

use App\Filament\Resources\MarketProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMarketProduct extends EditRecord
{
    protected static string $resource = MarketProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
