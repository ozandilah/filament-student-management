<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StudentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('class_id')
                ->live()
                ->relationship(name:'class', titleAttribute:'name',modifyQueryUsing:fn(Builder $query) => $query->orderBy('id','asc')),

                Select::make('section_id')
                ->label('Section')
                ->options(function(Get $get){
                    $classId = $get('class_id');
                    //Menerapkan dropdown bertingkat dimana section akan muncul berdasarkan id class
                   if($classId){
                    return Section::where('class_id', $classId)->get()->pluck('name','id')->toArray();
                   }
                }),

                TextInput::make('name')
                    ->autofocus()
                    ->required(),
                TextInput::make('email')
                    ->unique()
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('class.name')
                ->searchable()
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
                }),
                TextColumn::make('section.name')
                    ->badge()
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
