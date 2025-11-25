<?php

namespace App\Filament\Resources\MarketProductResource\Pages;

use App\Filament\Resources\MarketProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarketProducts extends ListRecords
{
    protected static string $resource = MarketProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

