<?php

namespace App\Filament\Resources;

use App\Enum\Product\ProductStatusEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Products';

    protected static ?string $label = 'Məhsul';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Select::make('category_id')->relationship('category', 'name')->required()->label('Kateqoriya'),
                    TextInput::make('code')->nullable()->label('Kodu'),
                    TextInput::make('name')->required()->label('Ad'),
                    TextInput::make('price')->required()->numeric()->label('Qiymət'),
                    TextInput::make('stock_count')->required()->numeric()->label('Məhsul sayı'),
                    TextInput::make('yeast_count')->required()->numeric()->label('Maya dəyəri'),
                    Select::make('status')
                        ->options([
                            ProductStatusEnum::ACTIVE->value => ProductStatusEnum::ACTIVE->getLabel(),
                            ProductStatusEnum::INACTIVE->value => ProductStatusEnum::INACTIVE->getLabel(),
                        ])
                        ->required()->label('Status')
                        ->default(ProductStatusEnum::ACTIVE->value)
                ]),
                Section::make([
                    FileUpload::make('images')->multiple()->required()
                        ->image()->label('Şəkli')
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            '16:9',
                            '4:3',
                            '1:1',
                        ]),
                    FileUpload::make('video')->nullable()->label('Video'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('code')->searchable()->label('Kodu'),
                Tables\Columns\TextColumn::make('name')->searchable()->label('Ad'),
                Tables\Columns\TextColumn::make('category.name')->searchable()->label('Kateqoriya'),
                Tables\Columns\TextColumn::make('price')->sortable()->label('Qiymət'),
                Tables\Columns\TextColumn::make('stock_count')->sortable()->label('Məhsul sayı'),
                Tables\Columns\TextColumn::make('yeast_count')->sortable()->numeric()->label('Maya dəyəri'),
                Tables\Columns\TextColumn::make('status')->badge()->label('Status'),
                Tables\Columns\ImageColumn::make('images')->label('Şəkli'),
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
                ]),
            ])->defaultSort('created_at', 'desc')->reorderable('sort_order');
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $modelClass = static::$model;

        return (string) $modelClass::count();
    }

}
