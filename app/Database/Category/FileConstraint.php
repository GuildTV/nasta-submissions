<?php

namespace App\Database\Category;

use App\Helpers\TextHelper;

use App\Exceptions\Database\ValueException;

use Illuminate\Database\Eloquent\Model;


class FileConstraint extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'file_constraints';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
    ];
}
