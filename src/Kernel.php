<?php

namespace Atlantis;

class Kernel
{
    function launch()
    {
        echo App::$router->resolve()->action();
    }
}
