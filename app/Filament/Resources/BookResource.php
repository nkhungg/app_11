<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class BookResource extends Resource {
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function getEloquentQuery(): Builder {
        return parent::getEloquentQuery()->where( 'user_id', Auth::id() );
    }

    public static function form( Form $form ): Form {
        return $form
        ->schema( [
            Forms\Components\TextInput::make( 'title' )
            ->required()
            ->maxLength( 255 ),
            Forms\Components\TextInput::make( 'author' )
            ->required()
            ->maxLength( 255 ),
            Forms\Components\Select::make( 'category_id' )
            ->relationship( 'category', 'category_name' )
            ->required()
            ->searchable()
            ->preload(),
            Forms\Components\TextInput::make( 'price' )
            ->label('Price (VND)')
                ->numeric()
                ->suffix('₫')
                ->minValue(1000)
                ->required(),
            Forms\Components\TextInput::make( 'stock' )
            ->numeric()
            ->required(),
            Forms\Components\TextInput::make( 'isbn' )
            ->required()
            ->unique( ignoreRecord: true )->disabledOn( 'edit' ),
            Forms\Components\Textarea::make( 'description' )
            ->nullable()
            ->rows( 5 ),
            Forms\Components\Repeater::make( 'images' )
            ->relationship( 'images' ) // Link to images relationship
            ->schema( [
                Forms\Components\FileUpload::make( 'path' )
                ->image()
                ->disk( 'public' )
                ->directory( 'books/covers' )
                ->nullable()
            ] )
            ->columnSpan( 'full' )
            ->label( 'Book Images' ),
        ] );
    }

    public static function table( Table $table ): Table {
        return $table
        ->columns( [
            Tables\Columns\ImageColumn::make('first_image')
                ->label('Cover')
                ->getStateUsing(function ($record) {
                    return $record->images->first()?->path;
                })
                ->disk('public')

            ->square(),
            Tables\Columns\TextColumn::make( 'title' )->searchable(),
            Tables\Columns\TextColumn::make( 'author' )->searchable(),
            Tables\Columns\TextColumn::make( 'category.category_name' ),
            Tables\Columns\TextColumn::make( 'price' )->label('Price')
            ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.') . ' ₫'),
            Tables\Columns\TextColumn::make( 'stock' ),
            Tables\Columns\TextColumn::make( 'isbn' ),
        ] )
        ->filters( [] )
        ->actions( [ Tables\Actions\EditAction::make() ] )
        ->bulkActions( [ Tables\Actions\DeleteBulkAction::make() ] );
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListBooks::route( '/' ),
            'create' => Pages\CreateBook::route( '/create' ),
            'edit' => Pages\EditBook::route( '/{record}/edit' ),
        ];
    }
}
