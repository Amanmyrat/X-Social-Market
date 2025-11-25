<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPurchase extends ViewRecord
{
    protected static string $resource = PurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Change Status'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Purchase Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('Purchase ID'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'delivered' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('price_tnt')
                            ->label('Price (TNT)')
                            ->suffix(' TNT')
                            ->color('success'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Purchased At')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('User Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.username')
                            ->label('Username'),
                        Infolists\Components\TextEntry::make('user.phone')
                            ->label('Phone'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Product Information')
                    ->schema([
                        Infolists\Components\ImageEntry::make('product.image_url')
                            ->label('Product Image')
                            ->disk('public')
                            ->height(200)
                            ->defaultImageUrl(url('/images/placeholder.png')),
                        Infolists\Components\TextEntry::make('product.name')
                            ->label('Product Name'),
                        Infolists\Components\TextEntry::make('product.description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}

