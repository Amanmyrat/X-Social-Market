<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Purchase;

class ListPurchases extends ListRecords
{
    protected static string $resource = PurchaseResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Purchases'),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Purchase::STATUS_PENDING))
                ->badge(Purchase::query()->where('status', Purchase::STATUS_PENDING)->count()),
            'delivered' => Tab::make('Delivered')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Purchase::STATUS_DELIVERED)),
            'cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Purchase::STATUS_CANCELLED)),
        ];
    }
}

