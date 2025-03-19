<?php

namespace App\Filament\Resources;

use App\Enum\Customer\CustomerStatusEnum;
use App\Enum\Partner\PartnerStatusEnum;
use App\Filament\Resources\PartnerResource\Pages;
use App\Models\Partner;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static ?string $label = 'Tərəfdaş';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    TextInput::make('name')->required()->label('Ad'),
                    TextInput::make('country_code')->default('+994')->disabled()->label('Ölkə kodu'),
                    TextInput::make('phone')->numeric()->required()->label('Telefon'),
                    TextInput::make('password')->label('Şifrə')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn ($state) => filled($state))
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->label('Şifrə')
                        ->required(fn (Page $livewire) => $livewire instanceof Pages\CreatePartner),
                    Select::make('status')->label('Status')
                        ->options([
                            PartnerStatusEnum::ACTIVE->value => PartnerStatusEnum::ACTIVE->getLabel(),
                            PartnerStatusEnum::INACTIVE->value => PartnerStatusEnum::INACTIVE->getLabel(),
                        ])
                        ->default(1)
                        ->required(),
                ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name')->label('Ad')->searchable(),
                Tables\Columns\TextColumn::make('country_code')->label('Ölkə kodu'),
                Tables\Columns\TextColumn::make('phone')->label('Telefon')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge()->label('Status'),
            ])
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
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
