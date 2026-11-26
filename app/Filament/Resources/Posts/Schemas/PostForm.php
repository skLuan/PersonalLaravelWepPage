<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\Post;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make('Content')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state, string $operation) {
                                        if ($operation === 'create' && blank($get('slug'))) {
                                            $set('slug', Str::slug((string) $state));
                                        }
                                    }),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->alphaDash()
                                    ->helperText('URL: /{locale}/blog/{slug}. Changing it breaks old links.')
                                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('locale', $get('locale'))),
                                TextInput::make('subtitle')
                                    ->maxLength(255),
                                Textarea::make('excerpt')
                                    ->rows(3)
                                    ->helperText('Summary shown in the list, feeds and as the default meta description.'),
                                RichEditor::make('body')
                                    ->fileAttachmentsDisk('blog')
                                    ->fileAttachmentsDirectory('content')
                                    ->fileAttachmentsVisibility('public')
                                    ->helperText('Headings start at H2: the post title is the page H1.')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('SEO')
                            ->description('Leave empty to use the title and excerpt.')
                            ->relationship('seo')
                            ->collapsible()
                            ->schema([
                                TextInput::make('title')
                                    ->label('Meta title')
                                    ->maxLength(70)
                                    ->hint(fn (?string $state) => Str::length((string) $state).' / 60'),
                                Textarea::make('description')
                                    ->label('Meta description')
                                    ->rows(2)
                                    ->maxLength(320)
                                    ->hint(fn (?string $state) => Str::length((string) $state).' / 160'),
                                TextInput::make('focus_keyword'),
                                TextInput::make('canonical_url')->url(),
                                Toggle::make('noindex')->label('Hide from search engines (noindex)'),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Publishing')
                            ->schema([
                                Select::make('locale')
                                    ->options(self::localeOptions())
                                    ->default(app()->getLocale())
                                    ->required()
                                    ->live(),
                                Select::make('translation_group')
                                    ->label('Translation of')
                                    ->helperText('Link this post to the same post in another language.')
                                    ->options(fn (Get $get, ?Post $record) => self::translationOptions($get('locale'), $record))
                                    ->searchable()
                                    ->placeholder('Not linked'),
                                DateTimePicker::make('published_at')
                                    ->label('Publish at')
                                    ->helperText('Empty = draft. A future date schedules it.'),
                                Select::make('author_id')
                                    ->relationship('author', 'name')
                                    ->default(fn () => auth()->id()),
                                Select::make('categories')
                                    ->relationship(
                                        'categories',
                                        'name',
                                        modifyQueryUsing: fn ($query, Get $get) => $query->where('locale', $get('locale')),
                                    )
                                    ->multiple()
                                    ->preload(),
                            ]),

                        Section::make('Cover image')
                            ->schema([
                                FileUpload::make('cover_image')
                                    ->hiddenLabel()
                                    ->image()
                                    ->disk('blog')
                                    ->directory('covers')
                                    ->visibility('public')
                                    ->imageEditor()
                                    ->maxSize(4096),
                                TextInput::make('cover_alt')
                                    ->label('Alt text')
                                    ->requiredWith('cover_image')
                                    ->maxLength(255),
                            ]),
                    ]),
            ]);
    }

    /** @return array<string, string> */
    public static function localeOptions(): array
    {
        $labels = ['en' => 'English', 'es' => 'Español'];

        return collect(config('app.available_locales', ['en']))
            ->mapWithKeys(fn (string $locale) => [$locale => $labels[$locale] ?? $locale])
            ->all();
    }

    /**
     * Posts in other languages, keyed by their translation group. The record's
     * own group is included so a saved value always has a label.
     *
     * @return array<string, string>
     */
    private static function translationOptions(?string $locale, ?Post $record): array
    {
        $options = Post::query()
            ->when($locale, fn ($query) => $query->where('locale', '!=', $locale))
            ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
            ->orderByDesc('published_at')
            ->get(['translation_group', 'locale', 'title'])
            ->mapWithKeys(fn (Post $post) => [$post->translation_group => "[{$post->locale}] {$post->title}"])
            ->all();

        if ($record && ! isset($options[$record->translation_group])) {
            $options = [$record->translation_group => 'Not linked'] + $options;
        }

        return $options;
    }
}
