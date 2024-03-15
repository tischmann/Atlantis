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
            view: 'error',
            layout: 'default',
            args: [
                'exception' => new Exception(
                    message: "Метод '{$class}\\{$name}()' не найден"
                ),
                'title' => get_str('not_found'),
                'code' => '404'
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
    protected function checkAdmin(string $type = 'html'): mixed
    {
        $is_admin = App::getUser()->isAdmin();

        switch (mb_strtolower($type)) {
            case 'json':
                if (!$is_admin) {
                    Response::json(
                        response: [
                            'title' => get_str('warning'),
                            'message' => get_str('access_denied')
                        ],
                        code: 403
                    );
                }
                break;
            case 'bool':
                return $is_admin;
            default:
                if (!$is_admin) {
                    View::send(
                        view: 'error',
                        layout: 'default',
                        args: [
                            'title' => get_str('access_denied'),
                            'code' => '403'
                        ],
                        exit: true,
                        code: 403
                    );
                }
                break;
        }

        return null;
    }
}
