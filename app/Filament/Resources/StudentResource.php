<?php

namespace App\Filament\Resources;

use App\Exports\StudentsExport;
use Filament\Forms;
use Filament\Tables;
use App\Models\Section;
use App\Models\Student;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StudentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Classes;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('class_id')
                ->live()
                ->relationship(name:'class', titleAttribute:'name',modifyQueryUsing:fn(Builder $query) => $query->orderBy('id','asc')),

                Select::make('section_id')
                ->searchable()
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
                    ->required(),
                TextInput::make('password')
                    ->password()
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
                Filter::make('class-section-filter')
                    ->form(
                        [
                            Select::make('class_id')
                                ->label('Filter By Class')
                                ->placeholder('Select a Class')
                                ->options(Classes::pluck('name','id')->toArray()),
                            Select::make('section_id')
                                ->label('Filter By Section')
                                ->placeholder('Select a Section')
                                ->options(function(Get $get) {
                                    $classId = $get('class_id');
                                    if($classId)
                                    {
                                        return Section::where('class_id', $classId)
                                                ->pluck('name','id')
                                                ->toArray();
                                    }
                                })
                        ]
                    )
                    ->query(function(Builder $query, array $data): Builder{
                        return $query->when($data['class_id'], function ($query) use ($data){
                            return $query->where('class_id',$data['class_id']);
                        })->when($data['section_id'], function($query) use ($data) {
                            return $query->where('section_id',$data['section_id']);
                        });
                    })
            ])
            ->actions([
                // Action::make('downloadPdf')
                //     ->url(function(Student $student) {
                //         return route('student.invoice.generate', $student);
                //     }),
                // Action::make('qrCode')
                //     ->url(function(Student $record) {
                //         return static::getUrl('qrCode', ['record' => $record]);
                //     }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('export')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-printer')
                    ->action(function(Collection $records) {
                        return Excel::download(new StudentsExport($records), 'student.xlsx');
                    })

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
