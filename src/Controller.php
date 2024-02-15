<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;

class Controller
{
    public function __call($name, $arguments): mixed
    {
        $class = get_class($this);

        View::send(
            view: '404',
            layout: 'default',
            args: [
                'exception' => new Exception(
                    message: "Метод '{$class}\\{$name}()' не найден"
                )
            ],
            exit: true
        );
    }

    /**
     * Проверка прав доступа администратора
     * 
     * @param bool $return Вернуть результат проверки
     * 
     * @return mixed Результат проверки или ничего
     */
    protected function checkAdminRights(bool $return = false): mixed
    {
        $is_admin = App::getCurrentUser()->isAdmin();

        if ($return) return $is_admin;

        if (!$is_admin) {
            View::send(
                view: '403',
                layout: 'default',
                exit: true
            );
        }
    }
}
