<?php

namespace Danupe\Core\Classes;

class Route
{
    public function getAll()
    {
        return danupe()->config()->getAllByKey('routes', 1);
    }
}
