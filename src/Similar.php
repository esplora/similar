<?php

declare(strict_types=1);

namespace Tabuna\Similar;

use Illuminate\Support\Collection;

/**
 * Class Similar
 */
class Similar
{
    /**
     * @var Collection
     */
    protected $matrix;

    /**
     * @var callable
     */
    protected $comparison;

    /**
     * Similar constructor.
     *
     * @param callable $comparison
     */
    public function __construct(callable $comparison)
    {
        $this->comparison = $comparison;
    }

    /**
     * @return Collection
     */
    private function getMatrix(): Collection
    {
        return $this->matrix;
    }

    /**
     * @param callable $comparison
     *
     * @return Similar
     */
    public function comparison(callable $comparison): Similar
    {
        $this->comparison = $comparison;

        return $this;
    }

    /**
     * @param Collection $titles
     *
     * @return Similar
     */
    private function create(Collection $titles): Similar
    {
        $this->matrix = $titles->transform(function ($item, $keyItem) use ($titles) {
            return $titles->filter(function ($title, $keyTitle) use ($item, $keyItem) {

                $comparison = $this->comparison;

                return $comparison($title, $item, $keyTitle, $keyItem);
            });
        });

        return $this;
    }

    /**
     * @return Similar
     */
    private function merge(): Similar
    {
        $this->matrix->transform(function (Collection $group) {
            return $this->matrix->map(function (Collection $simGroup) use ($group) {

                return $group->intersect($simGroup)->isNotEmpty()
                    ? $group->merge($simGroup)
                    : null;

            })->flatten()->filter()->unique();
        });

        return $this;
    }

    /**
     *
     * @return Similar
     */
    private function removeDuplicated(): Similar
    {
        $removes = [];

        $this->matrix->each(function (Collection $items, $keys) use (&$removes) {

            $this->matrix->each(function (Collection $collect, $keyCollect) use ($keys, $items, &$removes) {

                // The block being checked is larger, then it cannot be deleted
                if ($items->count() < $collect->count()) {
                    return;
                }

                // Completely identical blocks will be deleted later
                if ($items === $collect) {
                    return;
                }

                if ($collect->intersect($items)->isNotEmpty()) {
                    $removes[$keys][] = $keyCollect;
                    return;
                }
            });
        });


        $this->matrix = $this->matrix
            ->filter(function ($item, $key) use ($removes) {
                return isset($removes[$key]);
            })
            ->unique(function (Collection $item) {
                return $item->sort()->implode('~~~');
            });

        return $this;
    }

    /**
     * @param array $titles
     *
     * @return Similar
     */
    private function recoveryKeys(array $titles): Similar
    {
        $this->matrix = $this->matrix->map->keyBy(function ($value) use ($titles) {
            return array_search($value, $titles, true);
        });

        return $this;
    }

    /**
     * @param array $titles
     *
     * @return Collection
     */
    public function findOut(array $titles): Collection
    {
        return $this->create(collect($titles))
            ->merge()
            ->removeDuplicated()
            ->recoveryKeys($titles)
            ->getMatrix()
            ->sortByDesc(function (Collection $items) {
                return $items->count();
            });
    }
}
