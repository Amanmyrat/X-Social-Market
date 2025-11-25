<?php

namespace App\Filament\Resources\MarketProductResource\Pages;

use App\Filament\Resources\MarketProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMarketProduct extends CreateRecord
{
    protected static string $resource = MarketProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
