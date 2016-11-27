<?php
namespace App\Database\Traits;

trait HasPivotTrait
{
    public function hasPivot($relation, $model)
    {
        return (bool) $this->{$relation}()->wherePivot($model->getForeignKey(), $model->{$model->getKeyName()})->count();
    }
}