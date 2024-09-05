<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestStudent extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array  $columnSpan = "full";
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Student::query()
               ->latest()
               ->limit(5)
            )
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
            ]);
    }
}
