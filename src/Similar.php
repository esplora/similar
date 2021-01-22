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
        $this->matrix = $this->matrix
            ->filter(function (Collection $items, $keys) {
                $more = $items->map(function ($value) {
                    return (string)$value;
                });

                foreach ($this->matrix as $collect) {
                    $less = $collect->map(function ($value) {
                        return (string)$value;
                    });

                    if ($more->intersect($less)->isNotEmpty() && $more->count() < $less->count()) {
                        return false;
                    }
                }

                return true;
            })
            ->unique(function (Collection $item) {
                return $item
                    ->map(function ($value) {
                        return (string)$value;
                    })
                    ->sort()
                    ->implode('~~~');
            })
            ->filter(function (Collection $items) {
                return $items->isNotEmpty();
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
