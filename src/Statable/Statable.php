<?php

namespace Orobogenius\Statable;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;

trait Statable
{
    use Concerns\HasRelations;

    /**
     * Apply the given states to the model.
     *
     * @param mixed  $states
     * @return void
     */
    public function states($states)
    {
        $this->applyStates($states);

        return $this;
    }

    /**
     * Apply the active states to the model.
     *
     * @param mixed  $states
     * @param \Illuminate\Database\Eloquent\Relations\Relation  $relation
     * @return void
     */
    protected function applyStates($states, $relation = null)
    {
        $attributes = [];

        $model = $relation ? $relation->getModel() : $this;

        foreach (Arr::wrap($states) as $state) {
            if ($method = $this->modelHasState($model, $state)) {
                $attributes[] = $this->expandAttributes(
                    $model->$method()
                );
            }
        }

        $attributes = collect($attributes)->collapse();

        $this->transition($attributes->except('with_relations')->toArray(), $relation);

        if ($this->shouldTransformRelations($attributes)) {
            $this->transformRelations(
                $attributes->get('with_relations'),
                $relation ? $relation->first() : $model
            );
        }
    }

    /**
     * @param string  $state
     * @return string
     */
    protected function getMethodName($state)
    {
        return 'state'.ucfirst($state);
    }

    /**
     * Determine if the given model defines the given state.
     *
     * @param \Illuminate\Database\Eloquent\Model  $model
     * @param string  $state
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    protected function modelHasState($model, $state)
    {
        return tap($this->getMethodName($state), function ($method) use ($model, $state) {
            throw_unless(
                method_exists($model, $method),
                InvalidArgumentException::class,
                sprintf('Unable to locate [%s] state for [%s].', $state, static::class)
            );
        });
    }

    /**
     * Transition the model into the active state.
     *
     * @param array  $attributes
     * @param \Illuminate\Database\Eloquent\Relations\Relation  $relation
     * @return void
     */
    protected function transition($attributes, $relation = null)
    {
        $relation ? $relation->update($attributes)
                  : $this->fill($attributes)->save();
    }

    /**
     * Expand the attributes to their values.
     *
     * @param  array  $attributes
     * @return array
     */
    protected function expandAttributes($attributes)
    {
        foreach ($attributes as &$attribute) {
            if (is_callable($attribute) && ! is_string($attribute) && ! is_array($attribute)) {
                $attribute = $attribute($attributes);
            }

            if ($attribute instanceof Model) {
                $attribute = $attribute->getKey();
            }
        }

        return $attributes;
    }
}
