<?php

namespace Atlantis;

abstract class Date
{
    final static function valid($date): bool
    {
        return (bool) strtotime($date);
    }
}
