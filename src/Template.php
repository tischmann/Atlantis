<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use DateTime;

use Exception;

final class Template
{
    public static ?array $cached_args = null;

    public string $content = ''; // The content of the template file

    public function __construct(
        protected string $template,
        protected array $args = []
    ) {

        $this->content = $this->read();
    }

    public static function make(string $template, array $args = []): Template
    {
        return new static($template, $args);
    }

    public static function html(string $template, array $args = []): string
    {
        return (new static($template, $args))->render();
    }

    public static function echo(string $template, array $args = [])
    {
        echo (new static($template, $args))->render();
    }

    public function read(): string
    {
        $file = __DIR__ . '/../app/Views/' . $this->template . '.tpl';

        if (!file_exists($file)) {
            $file = __DIR__ . '/../app/Views/' . $this->template . '.php';

            if (!file_exists($file)) {
                throw new Exception('View file not found: ' . $this->template);
            }

            if (!in_array('ob_gzhandler', ob_list_handlers())) {
                ob_start('ob_gzhandler');
            } else {
                ob_start();
            }

            extract([...static::getCachedArgs(), ...$this->args]);

            require $file;

            return ob_get_clean();
        }

        return file_get_contents($file);
    }

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
            $search = $set[0];

            $key = $set[1];

            switch ($key) {
                case 'csrf':
                    $replace = Template::html('csrf');
                    break;
                case 'csrf-token':
                    $replace = CSRF::set()[1];
                    break;
                default:
                    if (!array_key_exists($key, $args)) {
                        if (substr($key, 0, 5) === 'lang=') {
                            $replace = substr($key, 5);
                            break;
                        } else {
                            continue 2;
                        }
                    }

                    $replace = $this->stringify($args[$key]);
                    break;
            }

            $parsed = str_replace($search, $replace, $parsed);
        }

        return $parsed;
    }

    private function stringify(mixed $value): string
    {
        switch (true) {
            case is_bool($value):
                return intval($value);
            case is_int($value):
            case is_float($value):
                return strval($value);
            case $value instanceof DateTime:
                return $value->format('Y-m-d H:i:s');
            case is_array($value):
            case is_object($value):
                return json_encode($value, 32 | 256) ?: '';
            default:
                return strval($value);
        }
    }

    public static function getCachedArgs(): array
    {
        if (static::$cached_args !== null) {
            return static::$cached_args;
        }

        $strings = [];

        foreach (Locale::getLocale(getenv('APP_LOCALE')) as $key => $value) {
            $strings["lang={$key}"] = $value;
        }

        $env = [];

        foreach (ENVIRONMENT_VARIABLES as $key) {
            $env["env=$key"] = getenv($key);
        }

        $pagination = new Pagination(limit: Pagination::DEFAULT_LIMIT);

        $request = new Request();

        $sorting = new Sorting(
            type: strval($request->request('sort')),
            order: strval($request->request('order'))
        );

        static::$cached_args = [
            ...$env,
            ...$strings,
            'nonce' => getenv('APP_NONCE'),
            'breadcrumbs' => '',
            'admin' => '',
            'pagination' => $pagination,
            'search' => strval($request->request('search')),
            'sorting' => $sorting,
        ];

        return static::$cached_args;
    }
}
