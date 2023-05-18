<?php

namespace Esplora\Similar\Tests;

class FixtureStingObject
{
    /**
     * @var string
     */
    protected $name;

    /**
     * FixtureStingObject constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
