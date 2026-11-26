<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->alphaDash()
                    ->unique(ignoreRecord: true)
                    ->helperText('Empty = generated from the title.'),
                Textarea::make('short_description')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('image_path')
                    ->label('Image URL')
                    ->url()
                    ->required(),
                TextInput::make('site_url')
                    ->label('Site URL')
                    ->url(),
                Select::make('skills')
                    ->relationship('skills', 'name')
                    ->multiple()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')->required()->unique('skills', 'name'),
                    ])
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->required()
                    ->rows(10)
                    ->columnSpanFull(),
            ]);
    }
}
