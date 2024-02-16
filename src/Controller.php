<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;

/**
 * Контроллер
 * 
 * @property Route $route Маршрут
 */
class Controller
{
    public Route $route;

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
            exit: true,
            code: 404
        );
    }

    /**
     * Проверка прав доступа администратора
     * 
     * В случае отсутствия прав доступа отправляет 403 ошибку
     * 
     * @return void
     */
    protected function checkAdminHtml(): void
    {
        if (!App::getCurrentUser()->isAdmin()) {
            View::send(
                view: '403',
                layout: 'default',
                exit: true,
                code: 403
            );
        }
    }
}
