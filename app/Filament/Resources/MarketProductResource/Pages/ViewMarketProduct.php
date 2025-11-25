<?php

namespace App\Filament\Resources\MarketProductResource\Pages;

use App\Filament\Resources\MarketProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMarketProduct extends ViewRecord
{
    protected static string $resource = MarketProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

