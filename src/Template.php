<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

final class Template
{
    protected array $each = [];

    protected static array $directives = [];

    protected static array $ifDirectives = [];

    public const REGEX_VARIABLE = '/\{{2}\s*\$(\w+)(\-\>)?(\w+)?(\(([\w\,\-]*)\))?\s*\}{2}/';

    public const REGEX_LAYOUT = '/\{{2}layout=([a-z0-9_\-\/]+)\}{2}/i';

    public const REGEX_INCLUDE = '/\{{2}include=([a-z0-9_\-\/]+)\}{2}/i';

    public const REGEX_SECTION = '/\{{2}(section)=(\w+)\}{2}((?>(?R)|.)*?)\{{2}\/\1\}{2}/is';

    public const REGEX_YIELD = '/\{{2}yield=([a-z0-9_\-\/]+)\}{2}/i';

    public const REGEX_FORM = '/<form\b[^>]*>(.*?)<\/form>/is';

    public const REGEX_LANG = '/\{{2}lang=(\w+)\}{2}/i';

    public const REGEX_ENV = '/\{{2}env=(\w+)\}{2}/i';

    public const REGEX_LOAD = '/\{{2}load=(.+)\}{2}/';

    public const REGEX_EACH = '/\{{2}(each)\s+\$(\w+)(\-\>)?(\w+)?(\(([\w\,\-]*)\))?\sas\s(?:\$(\w+)\s\=\>\s)?\$(\w+)\}{2}((?>(?R)|.)*?)\{{2}\/\1\}{2}/is';

    public const REGEX_IF = '/\{{2}(if)\s+(\!)?\$(\w+)(\-\>)?(\w+)?(\(([\w\,\-]*)\))?\s*(\={2}|\!\=|\<|\>|\<\=|\>\=|in|\!in)?\s*([^\}]*)\}{2}((?>(?R)|.)*?)\{{2}\/\1\}{2}/is';

    public const REGEX_IF_DIRECTIVE = '/\{{2}(if)\s+(\!)?(\w+)(?:\(([^|)]*)\))?\s*\}{2}((?>(?R)|.)*?)\{{2}\/\1\}{2}/is';

    public const REGEX_DIRECTIVE = '/(\{{2}(\w+)(?:\(([^|)]*)\))?\}{2})/is';

    public function __construct(
        protected string $view,
        protected array $args = []
    ) {
    }

    public static function ifDirective(string $name, callable $callback): void
    {
        static::$ifDirectives[$name] = $callback;
    }

    public static function directive(string $name, callable $callback): void
    {
        static::$directives[$name] = $callback;
    }

    /**
     * Отрисовка шаблона
     * 
     * @return string HTML-код шаблона
     */
    public function render(): string
    {
        $this->content = $this->raw();

        $this->parseLayout()
            ->parseIncludes()
            ->parseEnv()
            ->parseDirectives()
            ->parseIfDirectives()
            ->parseIf($this->content, $this->args)
            ->parseEach($this->content, $this->args)
            ->parseVariables($this->content, $this->args)
            ->parseTranslation()
            ->parseCSRF()
            ->parseLoad();

        return $this->content;
    }

    protected static function load(string $view): string
    {
        $path = getenv('APP_ROOT') . "/app/Views/{$view}.tpl";

        if (!file_exists($path)) {
            throw new \Exception("Представление не найдено: {$path}");
        }

        try {
            $content = file_get_contents($path);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        if ($content === false) {
            throw new \Exception(error_get_last()['message'] ?? '');
        }

        return $content;
    }

    public function raw(): string
    {
        return static::load($this->view);
    }

    /**
     * Парсинг макета
     * 
     * @return self 
     */
    protected function parseLayout(): self
    {
        if (preg_match(static::REGEX_LAYOUT, $this->content, $matches)) {
            $tag = $matches[0];
            $layout = $matches[1];
            $this->content = str_replace(
                $tag,
                static::load("layouts/{$layout}"),
                $this->content
            );
        }

        return $this;
    }

    protected function parseIncludes(): self
    {
        do {
            $sections = $this->parseSections();
            $this->fillSections($sections);
            $this->parseInclude();
        } while ($sections);

        return $this;
    }

    /**
     * Парсинг секций
     * 
     * @return array 
     */
    protected function parseSections(): array
    {
        $sections = [];

        preg_match_all(
            static::REGEX_SECTION,
            $this->content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $set) {
            $tag = $set[0];
            $key = $set[2];
            $value = $set[3];
            $sections[$key] = $value;
            $this->content = str_replace($tag, '', $this->content);
        }

        return $sections;
    }

    /**
     * Заполнение секций
     * 
     * @param array $sections Секции
     * @return self 
     */
    protected function fillSections(array $sections = []): self
    {
        if (!$sections) return $this;

        preg_match_all(
            static::REGEX_YIELD,
            $this->content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $set) {
            $tag = $set[0];
            $key = $set[1];
            $this->content = str_replace($tag, $sections[$key], $this->content);
        }

        return $this;
    }

    protected function parseInclude(): self
    {
        preg_match_all(
            static::REGEX_INCLUDE,
            $this->content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $set) {
            $tag = $set[0];

            $view = $set[1];

            $view = new View(view: $view, args: $this->args);

            $this->content = str_replace($tag, $view->render(), $this->content);
        }

        return $this;
    }

    protected function parseDirectives(): self
    {
        preg_match_all(
            static::REGEX_DIRECTIVE,
            $this->content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $set) {
            $tag = $set[0];

            $directive = $set[2];

            $args = explode(',', $set[3] ?? '');

            if (!array_key_exists($directive, static::$directives)) continue;

            $replace = static::$directives[$directive](...$args);

            $this->content = str_replace($tag, $replace, $this->content);
        }

        return $this;
    }

    protected function parseIfDirectives(): self
    {
        preg_match_all(
            static::REGEX_IF_DIRECTIVE,
            $this->content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $set) {
            $tag = $set[0];

            $inverse = boolval($set[2] ?? false);

            $directive = $set[3];

            $args = explode(',', $set[4] ?? '');

            $replace = $set[5];

            if (!array_key_exists($directive, static::$ifDirectives)) continue;

            $result = boolval(static::$ifDirectives[$directive](...$args));

            if ($inverse) $result = !$result;

            $this->content = str_replace(
                $tag,
                $result ? $replace : '',
                $this->content
            );
        }

        return $this;
    }

    /**
     * Парсинг условия
     * 
     * @return self
     */
    protected function parseIf(string &$content, array $args): self
    {
        preg_match_all(
            static::REGEX_IF,
            $content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $set) {
            $tag = $set[0];

            $inverse = boolval($set[2] ?? false);

            $variable = $set[3];

            $isObject = boolval($set[4] ?? false);

            $property = $set[5];

            $arguments = explode(',', $set[7]);

            $sign = $set[8];

            $value = $set[9];

            $replace = $set[10];

            if (array_key_exists($variable, $args)) {
                $variable = $args[$variable];

                if ($isObject) {
                    if (property_exists($variable, $property)) {
                        $variable = $variable->{$property};
                    } else if (method_exists($variable, $property)) {
                        $variable = $variable->{$property}(...$arguments);
                    } else {
                        continue;
                    }
                }
            } else if ($inverse && !$sign && !$value) {
                $content = str_replace($tag, $replace, $content);
                continue;
            } else {
                continue;
            }

            if ($inverse) {
                $replace = !$variable ? $replace : '';
            } elseif ($sign && $value != '') {
                $replace = match ($sign) {
                    '==' => $variable == $value ? $replace : '',
                    '!=' => $variable != $value ? $replace : '',
                    '>=' => $variable >= $value ? $replace : '',
                    '<=' => $variable <= $value ? $replace : '',
                    '>' => $variable > $value ? $replace : '',
                    '<' => $variable < $value ? $replace : '',
                    'in' => in_array($variable, explode(',', preg_replace('/\s*,\s*/', ',', $value))) ? $replace : '',
                    '!in' => !in_array($variable, explode(',', preg_replace('/\s*,\s*/', ',', $value))) ? $replace : '',
                    default => ''
                };
            } else {
                $replace = $variable ? $replace : '';
            }

            $content = str_replace($tag, $replace, $content);
        }

        return $this;
    }

    /**
     * Парсинг цикла
     * 
     * @return self
     */
    protected function parseEach(string &$content, array $args): self
    {
        preg_match_all(
            static::REGEX_EACH,
            $content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $set) {
            $name = $set[2];

            $iterable = $args[$name] ?? null;

            if (!$iterable) continue;

            $search = $set[0];

            $isObject = boolval($set[3] ?? false);

            $property = $set[4] ?? null;

            $arguments = explode(',', $set[6] ?? '');

            $key = $set[7] ?? null;

            $value = $set[8];

            $body = $set[9];

            if ($isObject) {
                if (property_exists($iterable, $property)) {
                    $iterable = $iterable->{$property};
                } else if (method_exists($iterable, $property)) {
                    $iterable = $iterable->{$property}(...$arguments);
                }
            }

            $replace = '';

            if (is_iterable($iterable) || is_object($iterable)) {
                foreach ($iterable as $k => $v) {
                    $bodyClone = $body;

                    $nestedArgs = [$value => $v];

                    if ($key) $nestedArgs[$key] = $k;

                    $nestedArgs = array_merge($this->args, $nestedArgs);

                    $this->parseIf($bodyClone, $nestedArgs)
                        ->parseEach($bodyClone, $nestedArgs)
                        ->parseVariables($bodyClone, $nestedArgs);

                    $replace .= $bodyClone;
                }
            } else {
                $replace = $this->stringifyVariable($iterable);
            }

            $content = str_replace($search, $replace, $content);
        }

        return $this;
    }

    /**
     * Парсинг строковых переменных локализации
     * 
     * @return self
     */
    protected function parseTranslation(): self
    {
        preg_match_all(
            static::REGEX_LANG,
            $this->content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $set) {
            $tag = $set[0];
            $key = $set[1];
            $this->content = str_replace(
                $tag,
                Locale::get($key),
                $this->content
            );
        }

        return $this;
    }

    /**
     * Парсинг переменных в теле шаблона
     * 
     * @return self
     */
    protected function parseVariables(string &$content, array $args): self
    {
        preg_match_all(
            static::REGEX_VARIABLE,
            $content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $set) {
            if (!array_key_exists($set[1], $args)) continue;

            $isObject = boolval($set[2] ?? false);

            $property = $set[3] ?? null;

            if ($isObject && !$property) continue;

            $search = $set[0];

            $name = $set[1];

            $arguments = explode(',', $set[5] ?? '');

            $variable = $args[$name];

            if ($isObject) {
                switch (true) {
                    case method_exists($variable, $property):
                        $variable = $variable->{$property}(...$arguments);
                        break;
                    case property_exists($variable, $property):
                        $variable = $variable->{$property};
                        break;
                    default:
                        continue 2;
                }
            }

            $replace = $this->stringifyVariable($variable);

            $content = str_replace(
                $search,
                $replace,
                $content
            );
        }

        return $this;
    }

    /**
     * Преобразование переменной в строку
     * 
     * @return string Строковое значение переменной
     */
    protected function stringifyVariable(mixed $variable): string
    {
        switch (gettype($variable)) {
            case 'string':
            case 'integer':
            case 'boolean':
            case 'double':
                return strval($variable);
            case 'object':
                if ($variable instanceof DateTime) {
                    return $variable->format('Y-m-d H:i:s');
                } else if ($variable instanceof Date) {
                    return $variable->format('Y-m-d');
                } else if ($variable instanceof Time) {
                    return $variable->format('H:i:s');
                }
            default:
                return json_encode($variable, 256);
        }
    }

    /**
     * Парсинг защиты от CSRF-атак в теле шаблона
     * 
     * @return self
     */
    protected function parseCSRF(): self
    {
        preg_match_all(
            static::REGEX_FORM,
            $this->content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $set) {
            $search = $set[1];

            list($key, $token) = CSRF::set();

            $replace = <<<EOL
            <input type="hidden" name="{$key}" value="{$token}"/>
            EOL;

            $this->content = str_replace(
                $search,
                $search . $replace,
                $this->content
            );
        }

        return $this;
    }

    /**
     * Парсинг переменных окружения в теле шаблона
     * 
     * @return self
     */
    protected function parseEnv(): self
    {
        preg_match_all(
            static::REGEX_ENV,
            $this->content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $set) {
            $tag = $set[0];
            $name = $set[1];
            $value = getenv(strtoupper($name)) ?: '';
            $this->content = str_replace($tag, $value, $this->content);
        }

        return $this;
    }

    /**
     * Парсинг загружаемых файлов в теле шаблона
     * 
     * @return self
     */
    protected function parseLoad(): self
    {
        preg_match_all(
            static::REGEX_LOAD,
            $this->content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $set) {
            $tag = $set[0];
            $path = $set[1];

            $path = getenv('APP_ROOT') . "/public/{$path}";

            if (!file_exists($path)) continue;

            try {
                $replacement = file_get_contents($path);
            } catch (\Exception $e) {
                $replacement = Locale::get('access_denied');
            }

            $this->content = str_replace($tag, $replacement, $this->content);
        }

        return $this;
    }
}
