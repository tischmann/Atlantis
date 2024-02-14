<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

class Controller
{
    /**
     * Вызов несуществующего метода
     * 
     */
    public function __call($name, $arguments): mixed
    {
        View::send(view: '404', layout: 'default', exit: true);
    }

    /**
     * Проверка прав доступа администратора
     * 
     */
    protected function __admin(): void
    {
        if (!App::getCurrentUser()->isAdmin()) {
            View::send(view: '403', layout: 'default', exit: true);
        }
    }
}
