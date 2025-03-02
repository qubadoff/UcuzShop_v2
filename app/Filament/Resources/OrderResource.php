<?php

namespace App\Filament\Resources;

use App\Enum\Order\OrderStatusEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use PrintFilament\Print\Infolists\Components\PrintComponent;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $label = 'Sifarişlər';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Select::make('customer_id')
                        ->relationship('customer', 'name')
                        ->required()->label('Müştəri'),
                    Select::make('partner_id')
                        ->relationship('partner', 'name')
                        ->required()->label('Tərəfdaş'),
                    TextInput::make('price')->numeric()->required()->label('Qiymət'),
                    Textarea::make('notes')->nullable()->label('Qeyd'),
                    TextInput::make('discount')->numeric()->default(0)->suffix(' %')->label('Endirim'),
                    Select::make('status')
                        ->options([
                            OrderStatusEnum::PENDING->value => OrderStatusEnum::PENDING->getLabel(),
                            OrderStatusEnum::CANCELLED->value => OrderStatusEnum::CANCELLED->getLabel(),
                            OrderStatusEnum::COMPLETED->value => OrderStatusEnum::COMPLETED->getLabel(),
                            OrderStatusEnum::DELIVERED->value => OrderStatusEnum::DELIVERED->getLabel(),
                            OrderStatusEnum::RETURNED->value => OrderStatusEnum::RETURNED->getLabel(),
                            OrderStatusEnum::PREPARING->value => OrderStatusEnum::PREPARING->getLabel(),
                        ])->label('Status')
                        ->default(1)
                        ->required(),
                ]),
                Section::make([
                    Repeater::make('orderProduct')
                        ->relationship()
                        ->required()
                        ->schema([
                            Select::make('product_id')
                                ->options(Product::all()->pluck('name', 'id'))
                                ->required()->label('Məhsul'),
                            TextInput::make('count')->required()->numeric()->label('Miqdar'),
                        ])->label('Məhsullar')
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('customer.name')->searchable(),
                Tables\Columns\TextColumn::make('price')->sortable()->money(' AZN'),
                Tables\Columns\TextColumn::make('discount')->sortable()->suffix(' %'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                ]),
            ])->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $modelClass = static::$model;

        return (string) $modelClass::count();
    }
}
