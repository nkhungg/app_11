<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class BooksRelationManager extends RelationManager {
    protected static string $relationship = 'books';

    public function form( Form $form ): Form {
        return $form
        ->schema( [
            Forms\Components\TextInput::make( 'title' )
            ->required()
            ->maxLength( 255 ),
            Forms\Components\TextInput::make( 'author' )
            ->required()
            ->maxLength( 255 ),
            Forms\Components\TextInput::make( 'price' )
            ->numeric()
            ->required(),
            Forms\Components\TextInput::make( 'stock' )
            ->numeric()
            ->required(),
            Forms\Components\TextInput::make( 'isbn' )
            ->required()
            ->unique()->disabledOn('edit'),
            Forms\Components\Textarea::make( 'description' )
            ->nullable()
            ->rows( 5 ),
            Forms\Components\FileUpload::make( 'image' )
            ->image()
            ->directory( 'books/covers' )
            ->maxSize( 1024 )
            ->nullable(),
        ] );
    }

    public function table( Table $table ): Table {
        return $table
            ->query(function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->square(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('author')->searchable(),
                Tables\Columns\TextColumn::make('price')->money('usd'),
                Tables\Columns\TextColumn::make('stock'),
                Tables\Columns\TextColumn::make('isbn'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
