<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->placeholder('Masukkan nama'),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->placeholder('Masukkan email'),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->required()
                    ->placeholder('Masukkan password')
                    ->password(),
                Forms\Components\TextInput::make('phone')
                    ->label('Phone')
                    ->placeholder('Masukkan nomor telepon'),
                Forms\Components\TextInput::make('address')
                    ->label('Address')
                    ->placeholder('Masukkan alamat'),
                Forms\Components\Select::make('role_id')
                    ->label('Role')
                    ->required()
                    ->options([
                        1 => 'Super Admin',
                        2 => 'Admin',
                        3 => 'Komunitas',
                        4 => 'Pemilik Toko',
                        5 => 'Karyawan Toko',
                        6 => 'Tamu',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // Disabled filter
                Tables\Filters\SelectFilter::make('disabled_at')
                    ->label('Active')
                    ->options([
                        'NOT NULL' => 'Yes',
                        'NULL' => 'No',
                    ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
