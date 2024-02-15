<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Представление
 */
final class View
{
    protected Template $template_instance;

    /**
     * @param string $template Шаблон
     * @param array $args Аргументы
     * @param string $layout Макет
     */
    public function __construct(
        public string $template,
        public array $args = [],
        public string $layout = 'default'
    ) {
        $this->template_instance = new Template(
            template: "layouts/{$this->layout}",
            args: [
                ...$this->args,
                'body' => Template::html(
                    template: $this->template,
                    args: $this->args
                ),
            ]
        );
    }

    /**
     * Создаёт экземпляр класса
     *
     * @param string $view Шаблон
     * @param array $args Аргументы
     * @param string $layout Макет
     * @return View
     */
    public static function make(
        string $view,
        array $args = [],
        string $layout = 'default'
    ): View {
        return new static($view, $args, $layout);
    }

    /**
     * Возвращает рендеринг представления в виде HTML строки
     *
     * @param string $view Шаблон
     * @param array $args Аргументы
     * @param string $layout Макет
     * @return string
     */
    public static function html(
        string $view,
        array $args = [],
        string $layout = 'default'
    ): string {
        return static::make($view, $args, $layout)->render();
    }

    /**
     * Выводит рендеринг представления в виде HTML строки в поток вывода с заголовками
     *
     * @param string $view Шаблон
     * @param array $args Аргументы
     * @param string $layout Макет
     * @param bool $exit Завершить выполнение
     * @return void
     */
    public static function send(
        string $view,
        array $args = [],
        string $layout = 'default',
        bool $exit = false
    ) {
        Response::send(static::html($view, $args, $layout));

        if ($exit) exit;
    }

    /**
     * Выводит рендеринг представления в виде HTML строки в поток вывода
     *
     * @param string $view Шаблон
     * @param array $args Аргументы
     * @param string $layout Макет
     * @param bool $exit Завершить выполнение
     * @return void
     */
    public static function echo(
        string $view,
        array $args = [],
        string $layout = 'default',
        bool $exit = false
    ) {
        echo static::make($view, $args, $layout)->render();

        if ($exit) exit;
    }

    /**
     * Возвращает рендеринг представления в виде HTML строки
     *
     * @return string
     */
    public function render(): string
    {
        return $this->template_instance->render();
    }
}
