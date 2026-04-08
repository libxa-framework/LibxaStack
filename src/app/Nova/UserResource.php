<?php

declare(strict_types=1);

namespace App\Nova;

use Libxa\Nova\Resource;
use Libxa\Nova\Field;
use App\Models\User;

class UserResource extends Resource
{
    /**
     * The model the resource corresponds to.
     */
    public static string $model = User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     */
    public static string $title = 'name';

    /**
     * The columns that should be searched.
     */
    public static array $search = [
        'id', 'name', 'email',
    ];

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(): array
    {
        return [
            Field::make('ID')->sortable(),
            
            Field::make('Name')->sortable(),
            
            Field::make('Email')->sortable(),
            
            Field::make('Password')
                ->hideFromIndex(),
        ];
    }

    /**
     * Get the display name of the resource.
     */
    public static function label(): string
    {
        return 'Users';
    }
}
