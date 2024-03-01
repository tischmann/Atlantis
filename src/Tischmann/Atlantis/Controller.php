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
     * @param string $type Тип ответа
     * @return void
     */
    protected function checkAdmin(string $type = 'html'): void
    {
        if (!App::getCurrentUser()->isAdmin()) {
            switch (mb_strtolower($type)) {
                case 'json':
                    Response::json(
                        response: [
                            'title' => get_str('warning'),
                            'message' => get_str('access_denied')
                        ],
                        code: 403
                    );
                    break;
                default:
                    View::send(
                        view: '403',
                        layout: 'default',
                        exit: true,
                        code: 403
                    );
                    break;
            }
        }
    }
}
