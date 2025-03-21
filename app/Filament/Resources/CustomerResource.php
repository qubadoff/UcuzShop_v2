<?php

namespace App\Filament\Resources;

use App\Enum\Customer\CustomerStatusEnum;
use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Exception;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $label = 'Müştəri';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    TextInput::make('name')->required()->label('Ad'),
                    TextInput::make('country_code')->default('+994')->disabled()->label('Ölkə kodu'),
                    TextInput::make('phone')->numeric()->required()->label('Telefon'),
                    TextInput::make('location')->nullable()->label('Lokasiya')->required(),
                    TextInput::make('password')->label('Şifrə')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn ($state) => filled($state))
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->label('Şifrə')
                        ->required(fn (Page $livewire) => $livewire instanceof Pages\CreateCustomer),
                    Select::make('status')
                        ->options([
                            CustomerStatusEnum::ACTIVE->value => CustomerStatusEnum::ACTIVE->getLabel(),
                            CustomerStatusEnum::INACTIVE->value => CustomerStatusEnum::INACTIVE->getLabel(),
                        ])
                        ->default(1)
                        ->required(),
                ])->columns(3),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name')->label('Ad')->searchable(),
                Tables\Columns\TextColumn::make('country_code')->label('Ölkə kodu'),
                Tables\Columns\TextColumn::make('phone')->label('Telefon')->searchable(),
                Tables\Columns\TextColumn::make('location')->label('Lokasiya'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $modelClass = static::$model;

        return (string) $modelClass::count();
    }
}
