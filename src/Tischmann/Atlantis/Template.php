<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Шаблонизатор
 */
final class Template
{
    public static ?array $cached_args = null;

    public string $content = '';

    /**
     * @param string $template Шаблон
     * @param array $args Аргументы
     */
    public function __construct(
        public string $template,
        public array $args = []
    ) {

        $this->content = $this->read();
    }

    /**
     * Создаёт экземпляр класса
     *
     * @param string $template Шаблон
     * @param array $args Аргументы
     * @return Template
     */
    public static function make(string $template, array $args = []): Template
    {
        return new static($template, $args);
    }

    /**
     * Возвращает рендеринг шаблона в виде HTML строки
     *
     * @param string $template Шаблон
     * @param array $args Аргументы
     * @return string
     */
    public static function html(string $template, array $args = []): string
    {
        return (new static($template, $args))->render();
    }

    /**
     * Выводит рендеринг шаблона в виде HTML строки в поток вывода
     *
     * @param string $template Шаблон
     * @param array $args Аргументы
     * @return void
     */
    public static function echo(string $template, array $args = [])
    {
        echo (new static($template, $args))->render();
    }

    /**
     * Читает содержимое шаблона
     *
     * @return string Содержимое шаблона
     */
    public function read(): string
    {
        $template_php_file = __DIR__ . '/../../../app/Views/' . $this->template . '.php';

        if (!in_array('ob_gzhandler', ob_list_handlers())) {
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }

        if (file_exists($template_php_file)) {
            extract([...static::getCachedArgs(), ...$this->args]);

            include $template_php_file;
        } else {
            $message = get_str('template_not_found') . ": '{$this->template}'";

            echo <<<HTML
            <div title="{$message}" style="padding:8px; text-align:center; background:red !important; color: white !important">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="32px" height="32px" style="display:inline-block">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            HTML;
        }

        $buffer = ob_get_clean();

        if ($buffer === false) {
            die('Ошибка при чтении из буфера вывода');
        }

        return $buffer;
    }

    /**
     * Рендеринг шаблона
     *
     * @return string HTML строка
     */
    public function render(): string
    {
        $args = [...static::getCachedArgs(), ...$this->args];

        $parsed = $this->content;

        preg_match_all(
            '/{{2}([^}]+)}{2}/',
            $parsed,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $set) {
            $subject = $set[0];

            $key = $set[1];

            switch ($key) {
                case 'csrf':
                    $csrf = csrf_set();

                    $replace = <<<HTML
                    <input type="hidden" name="{$csrf->key}" value="{$csrf->token}" />
                    HTML;
                    break;
                case 'csrf-token':
                    $replace = csrf_set()->token;
                    break;
                case 'pagination':
                    $replace = Template::html(template: 'pagination', args: $args);
                    break;
                default:
                    $replace = $this->parse($key, $args);
                    break;
            }

            $parsed = str_replace($subject, $replace, $parsed);
        }

        return $parsed;
    }

    /**
     * Парсинг тэгов шаблона
     *
     * @param string $key Ключ
     * @param array $args Аргументы
     * @return string
     */
    private function parse(string $key, array $args): string
    {
        if (array_key_exists($key, $args)) {
            return  $this->stringify($args[$key]);
        }

        return match (true) {
            substr($key, 0, 5) === "date=" => date(substr($key, 5)),
            default => $key
        };
    }

    /**
     * Преобразует значение в строку
     *
     * @param mixed $value Значение
     * @return string
     */
    private function stringify(mixed $value): string
    {
        if ($value === null) return '';

        switch (true) {
            case is_bool($value):
                return intval($value);
            case is_int($value):
            case is_float($value):
                return strval($value);
            case $value instanceof \DateTime:
                return strval($value?->format('Y-m-d H:i:s'));
            case $value instanceof DateTime:
                return strval($value?->format('Y-m-d H:i:s'));
            case $value instanceof Date:
                return strval($value?->format('Y-m-d'));
            case $value instanceof Time:
                return strval($value?->format('H:i:s'));
            case is_array($value):
            case is_object($value):
                return strval(json_encode($value, 32 | 256));
            default:
                return strval($value);
        }
    }

    /**
     * Возвращает кэшированные аргументы для шаблона
     *
     * @return array Аргументы
     */
    public static function getCachedArgs(): array
    {
        if (static::$cached_args !== null) return static::$cached_args;

        $strings = [];

        $locale = getenv('APP_LOCALE') ?: 'ru';

        foreach (Locale::getLocale($locale) as $key => $value) {
            $strings["lang={$key}"] = $value;
        }

        $env = [];

        foreach (ENVIRONMENT_VARIABLES as $key) {
            $env["env=$key"] = getenv($key);
        }

        $uniqid = uniqid(more_entropy: true);

        static::$cached_args = [
            ...$env,
            ...$strings,
            'nonce' => getenv('APP_NONCE'),
            'title' => App::getTitle(),
            'tags' => App::getTags(),
            'uniqid' => $uniqid,
        ];

        return static::$cached_args;
    }
}
