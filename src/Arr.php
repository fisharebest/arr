<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest;

use ArrayObject;
use Closure;

use function array_filter;
use function array_map;
use function array_unique;
use function array_values;
use function uasort;

/**
 * Fluent arrays
 *
 * @template TKey of array-key
 * @template TValue
 * @extends  ArrayObject<TKey,TValue>
 */
final class Arr extends ArrayObject
{
    /**
     * @param Arr<TKey,TValue> $arr2
     *
     * @return self<TKey,TValue>
     */
    public function concat(Arr $arr2): self
    {
        $arr1 = array_values($this->getArrayCopy());
        $arr2 = array_values($arr2->getArrayCopy());

        /** @var Arr<TKey,TValue> */
        return new self($arr1 + $arr2);
    }

    /**
     * @param Closure(TValue):bool $closure
     *
     * @return self<TKey,TValue>
     */
    public function filter(Closure $closure): self
    {
        /** @var Arr<TKey,TValue> */
        return new self(array_filter($this->getArrayCopy(), $closure));
    }

    /**
     * @return self<int,mixed>
     */
    public function flatten(): self
    {
        $flat = [];

        foreach ($this->getArrayCopy() as $item) {
            if ($item instanceof self) {
                foreach ($item as $value) {
                    $flat[] = $value;
                }
            } else {
                $flat[] = $item;
            }
        }

        return new self($flat);
    }

    /**
     * @param null|Closure(TValue):bool $closure
     *
     * @return TValue|null
     */
    public function first(Closure $closure = null): mixed
    {
        foreach ($this->getArrayCopy() as $value) {
            if ($closure === null || $closure($value)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param null|Closure(TValue):bool $closure
     *
     * @return TValue|null
     */
    public function last(Closure $closure = null): mixed
    {
        return $this->reverse()->first($closure);
    }

    /**
     * @template T
     *
     * @param Closure(TValue):T $closure
     *
     * @return self<TKey,T>
     */
    public function map(Closure $closure): self
    {
        /** @var Arr<TKey,T> */
        return new self(array_map($closure, $this->getArrayCopy()));
    }

    /**
     * @return self<TKey,TValue>
     */
    public function reverse(): self
    {
        /** @var Arr<TKey,TValue> */
        return new self(array_reverse($this->getArrayCopy()));
    }

    /**
     * @param Closure(TValue,TValue):int $closure
     *
     * @return self<TKey,TValue>
     */
    public function sort(Closure $closure): self
    {
        $arr = $this->getArrayCopy();
        uasort($arr, $closure);

        /** @var Arr<TKey,TValue> */
        return new self($arr);
    }

    /**
     * @param Closure(TKey,TKey):int $closure
     *
     * @return self<TKey,TValue>
     */
    public function sortKeys(Closure $closure): self
    {
        $arr = $this->getArrayCopy();
        uksort($arr, $closure);

        /** @var Arr<TKey,TValue> */
        return new self($arr);
    }

    /**
     * @return self<TKey,TValue>
     */
    public function unique(): self
    {
        /** @var Arr<TKey,TValue> */
        return new self(array_unique($this->getArrayCopy()));
    }
}
