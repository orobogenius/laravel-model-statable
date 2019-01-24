<?php

namespace Orobogenius\Statable\Concerns;

use Illuminate\Database\Eloquent\Collection;

trait HasRelations
{
    /**
     * Determine if the model's relations should be transformed.
     *
     * @param  array  $attributes
     * @return bool
    */
    protected function shouldTransformRelations($attributes)
    {
        return $this->hasRelations($attributes);
    }

    /**
     * Check if the attributes has relations to transform.
     *
     * @param  array  $attributes
     * @return bool
    */
    protected function hasRelations($attributes)
    {
        return isset($attributes['with_relations']);
    }

    /**
     * Transform the given relations into their defined states.
     *
     * @param array  $relations
     * @param \Illuminate\Database\Eloquent\Model  $model
     * @return void
    */
    protected function transformRelations($relations, $model)
    {
        foreach ($relations as $relationship => $states) {
            if (method_exists($model, $relationship)) {
                $this->applyStates($states, $model->$relationship());
            }
        }
    }
}
