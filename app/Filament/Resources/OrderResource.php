<?php

namespace App\Filament\Resources;

use App\Enum\Order\OrderStatusEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                    Textarea::make('note')->nullable()->label('Qeyd'),
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
                ])->columns(3),

                Section::make([
                    Repeater::make('orderProduct')
                        ->relationship()
                        ->required()
                        ->schema([
                            Select::make('product_id')
                                ->options(Product::all()->pluck('name', 'id'))
                                ->required()->label('Məhsul'),
                            TextInput::make('count')->required()->numeric()->label('Miqdar'),
                        ])->label('Məhsullar')->columns()
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('customer.name')->searchable()->label('Müştəri')->searchable(),
                Tables\Columns\TextColumn::make('partner.name')->searchable()->label('Tərəfdaş')->searchable(),
                Tables\Columns\TextColumn::make('price')->sortable()->suffix(' AZN')->label('Qiymət'),
                Tables\Columns\TextColumn::make('discount')->sortable()->suffix(' %')->label('Endirim'),
                Tables\Columns\SelectColumn::make('status')->options([
                    OrderStatusEnum::PENDING->value => OrderStatusEnum::PENDING->getLabel(),
                    OrderStatusEnum::PREPARING->value => OrderStatusEnum::PREPARING->getLabel(),
                    OrderStatusEnum::DELIVERED->value => OrderStatusEnum::DELIVERED->getLabel(),
                    OrderStatusEnum::COMPLETED->value => OrderStatusEnum::COMPLETED->getLabel(),
                    OrderStatusEnum::CANCELLED->value => OrderStatusEnum::CANCELLED->getLabel(),
                    OrderStatusEnum::RETURNED->value => OrderStatusEnum::RETURNED->getLabel(),
                ])->label('Status'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Yaradılma Tarixi'),
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
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make()
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
