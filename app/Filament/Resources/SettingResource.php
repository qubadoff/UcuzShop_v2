<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationGroup = 'Ayarlar';

    protected static ?string $navigationLabel = 'Ayarlar';

    protected static ?string $label = 'Ayar';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    TextInput::make('phone')->label('Telefon'),
                    TextInput::make('email')->label('Email'),
                    TextInput::make('location')->label('Ünvan'),
                    TextInput::make('working_hours')->label('İş saatları'),
                    TextInput::make('min_order_price')->label('Minimum sifariş məbləği'),
                    Select::make('is_stock_minus')->options([
                        1 => 'Hə',
                        2 => 'Yox'
                    ])->label('Anbar mənfiyə getsin ?'),
                    TimePicker::make('operation_hour')->label('Kassa açılış vaxtı'),
                ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('phone')->label('Telefon'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('location')->label('Ünvan'),
                Tables\Columns\TextColumn::make('working_hours')->label('İş saatları'),
                Tables\Columns\TextColumn::make('min_order_price')->label('Minimum sifariş məbləği'),
                Tables\Columns\SelectColumn::make('is_stock_minus')->options([
                    1 => 'Hə',
                    2 => 'Yox'
                ])->label('Anbar mənfiyə getsin ?'),
                Tables\Columns\TextColumn::make('operation_hour')->label('Kassa açılış vaxtı'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
