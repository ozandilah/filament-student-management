<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Section;
use Filament\Forms\Form;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SectionResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SectionResource\RelationManagers;
use Filament\Tables\Columns\TextColumn;

class SectionResource extends Resource
{
    protected static ?string $model = Section::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('class_id')
                    ->relationship(name:'class', titleAttribute:'name',modifyQueryUsing:fn(Builder $query) => $query->orderBy('id','asc')),
                TextInput::make('name')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                BadgeColumn::make('class.name')
                ->color(static function ($state): string {
                    $className = $state;
                    $classNumber = (int) str_replace('Class', '', $className);
                    if ($classNumber >= 1 && $classNumber <= 2) {
                        return 'primary';
                    } else if($classNumber >= 3 && $classNumber <=4) {
                        return 'warning';
                    } else if($classNumber >=5 && $classNumber <=6) {
                        return 'success';
                    } else if($classNumber >=7 && $classNumber <=10) {
                        return 'danger';
                    } else {
                        return 'secondary';
                    }
                })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSections::route('/'),
            'create' => Pages\CreateSection::route('/create'),
            'edit' => Pages\EditSection::route('/{record}/edit'),
        ];
    }
}
