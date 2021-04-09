<?php

namespace Atlantis;

use Atlantis\Controllers\Controller;
use stdClass;

final class Template
{
    public string $content = '';
    public array $sections = [];
    public string $uniqid = '';
    public array $args = [];

    const REGEX_VARIABLE = '/\[\$((?>(?R)|.)*?)\]/is';
    const REGEX_METHODS = '/(\w+)+(\(\))?/i';
    const REGEX_LAYOUT = '/\[layout=([a-z0-9_\-\/]+)\]/i';
    const REGEX_INCLUDE = '/\[include=([a-z0-9_\-\/]+)\]/i';
    const REGEX_SECTION = '/\[(section)=([a-z0-9_-]+)\]((?>(?R)|.)*?)\[\/\1\]/is';
    const REGEX_YIELD = '/\[yield=([a-z0-9_\-\/]+)\]/i';
    const REGEX_CSRF = '/(\[csrf\])/i';
    const REGEX_CSRF_TOKEN = '/(\[csrf-token\])/i';
    const REGEX_NOT_AUTH = '/\[(!auth)\]((?>(?R)|.)*?)\[\/\1\]/is';
    const REGEX_AUTH = '/\[(auth)\]((?>(?R)|.)*?)\[\/\1\]/is';
    const REGEX_NOT_ADMIN = '/\[(!admin)\]((?>(?R)|.)*?)\[\/\1\]/is';
    const REGEX_ADMIN = '/\[(admin)\]((?>(?R)|.)*?)\[\/\1\]/is';
    const REGEX_LANG = '/\[lang=([a-z0-9_-]+)\]/';
    const REGEX_TITLE = '/(\[title\])/i';
    const REGEX_ERROR = '/\[error\]/i';
    const REGEX_DATE = '/\[date=(.+)\]/i';
    const REGEX_ENV = '/\[env=([A-Z0-9_\-]+)\]/';
    const REGEX_LOAD = '/\[load=(.+)\]/';
    const REGEX_UNIQID = '/(\[uniqid\])/i';
    const REGEX_NONCE = '/(\[nonce\])/i';
    const REGEX_FOREACH = '/\[(foreach)\((\$[a-z0-9_-]+)\s*as\s*(\$[a-z0-9_-]+)\s*\)]((?>(?R)|.)*?)\[\/\1\]/is';
    const REGEX_IF = '/\[(if)\s*((?>(?R)|.)*?)\]((?>(?R)|.)*?)\[\/\1\]/is';
    const REGEX_IF_CONDITION = '/(!{0,2})\$((?>(?R)|.)*?)\s*$|\s+(\!\={1,2}|\={1,3}|\>|\<|\>\=|\<\=|\<\>)\s+(\S+)/i';
    const REGEX_ELSEIF = '/\[(elseif)\s*((?>(?R)|.)*?)\]((?>(?R)|.)*?)\[\/\1\]/is';
    const REGEX_ELSE = '/\[(else)\]((?>(?R)|.)*?)\[\/\1\]/is';

    public function __construct(string $name, array $args = [])
    {
        $this->args = $args;
        $this->content = $this->include($name);
        $this->uniqid = uniqid();
    }

    protected function parse()
    {
        $this->parseLayout()
            ->parseChilds()
            ->parseNotAuth()
            ->parseNotAdmin()
            ->parseAuth()
            ->parseAdmin()
            ->parseStrings()
            ->parseIf()
            ->parseForEach()
            ->parseVariables()
            ->parseTitle()
            ->parseDate()
            ->parseUniqid()
            ->parseNonce()
            ->parseCSRF()
            ->parseEnv()
            ->parseLoad();
    }

    public function render()
    {
        $this->parse();
        return $this->content;
    }

    public function include(string $view)
    {
        $path = "../views/{$view}.tpl.php";

        if (!file_exists($path)) {
            Response::response(new Error(
                message: Language::get('error_view_not_found') . ": $view",
                type: 'warning'
            ));
        }

        return file_get_contents($path);
    }

    protected function parseLayout()
    {
        if (preg_match(self::REGEX_LAYOUT, $this->content, $matches)) {
            $this->content = str_replace(
                $matches[0],
                $this->include("layouts/{$matches[1]}"),
                $this->content
            );
        }

        return $this;
    }

    protected function parseChilds()
    {
        do {
            $exist = $this->parseSections();
            $this->fillSections();
            $this->parseIncludes();
        } while ($exist);

        return $this;
    }

    protected function parseInclude(string $content): bool
    {
        if (preg_match_all(self::REGEX_INCLUDE, $content, $matches)) {
            foreach ($matches[1] as $key => $view) {
                $this->content = str_replace(
                    $matches[0][$key],
                    View::render($view, $this->args),
                    $this->content
                );
            }

            return true;
        }

        return false;
    }

    protected function parseIncludes(): bool
    {
        do {
            $exist = $this->parseInclude($this->content);
        } while ($exist);

        return false;
    }

    protected function parseSections(): bool
    {
        if (!preg_match_all(self::REGEX_SECTION, $this->content, $matches)) {
            return false;
        }

        foreach ($matches[2] as $key => $view) {
            $this->sections[$matches[2][$key]] = $matches[3][$key];
            $this->content = str_replace(
                $matches[0][$key],
                '',
                $this->content
            );
        }

        return true;
    }

    protected function fillSections(): bool
    {
        if (!$this->sections) {
            return false;
        }

        if (!preg_match_all(self::REGEX_YIELD, $this->content, $matches)) {
            return false;
        }

        foreach ($matches[1] as $key => $section) {
            $this->content = str_replace(
                $matches[0][$key],
                $this->sections[$section],
                $this->content
            );
        }

        return true;
    }

    protected function parseNotAuth()
    {
        if (preg_match_all(self::REGEX_NOT_AUTH, $this->content, $matches)) {
            foreach ($matches[2] as $key => $val) {
                $this->content = str_replace(
                    $matches[0][$key],
                    Auth::signedIn() ? '' : $val,
                    $this->content
                );
            }
        }

        return $this;
    }

    protected function parseNotAdmin()
    {
        if (preg_match_all(self::REGEX_NOT_ADMIN, $this->content, $matches)) {
            foreach ($matches[2] as $key => $val) {
                $this->content = str_replace(
                    $matches[0][$key],
                    Auth::isAdmin() ? '' : $val,
                    $this->content
                );
            }
        }

        return $this;
    }

    protected function parseAuth()
    {
        if (preg_match_all(self::REGEX_AUTH, $this->content, $matches)) {
            foreach ($matches[2] as $key => $val) {
                $this->content = str_replace(
                    $matches[0][$key],
                    Auth::signedIn() ? $val : '',
                    $this->content
                );
            }
        }

        return $this;
    }

    protected function parseAdmin()
    {
        if (preg_match_all(self::REGEX_ADMIN, $this->content, $matches)) {
            foreach ($matches[2] as $key => $val) {
                $this->content = str_replace(
                    $matches[0][$key],
                    Auth::isAdmin() ? $val : '',
                    $this->content
                );
            }
        }

        return $this;
    }

    protected function parseStrings()
    {
        if (preg_match_all(self::REGEX_LANG, $this->content, $matches)) {
            foreach ($matches[1] as $key => $val) {
                $this->content = str_replace(
                    $matches[0][$key],
                    Language::get($val),
                    $this->content
                );
            }
        }

        return $this;
    }

    protected function parseVariables(array $variables = null, string $content = null)
    {
        $variables = $variables ?? $this->args;
        $contentToParse = $content ?? $this->content;

        foreach ($variables as $name => $val) {
            if (preg_match_all(self::REGEX_VARIABLE, $contentToParse, $matches)) {
                foreach ($matches[0] as $key => $tag) {
                    preg_match_all(self::REGEX_METHODS, $matches[1][$key], $methods);
                    $value = $val;

                    if (($methods[1][0] ?? false) != $name) {
                        continue;
                    }

                    foreach ($methods[1] as $methodKey => $method) {
                        if ($method == $name) {
                            continue;
                        }

                        if ($methods[2][$methodKey]) {
                            $value = $value->$method();
                        } else {
                            $value = $value->$method;
                        }
                    }

                    if ((string) $value != $value) {
                        Response::response(new Error(
                            status: 500,
                            message: Language::get('error_bad_template_variable1')
                        ));
                    }

                    $contentToParse = str_replace(
                        $tag,
                        $value,
                        $contentToParse
                    );
                }
            }
        }

        if ($content !== null) {
            return $contentToParse;
        } else {
            $this->content = $contentToParse;
        }

        return $this;
    }

    protected function getIfCondition(string $condition): bool
    {
        if (!preg_match(self::REGEX_IF_CONDITION, $condition, $matches)) {
            return false;
        }

        $exp = explode(' ', $matches[2]);
        $inversion = $matches[1];
        $variable = trim($exp[0]);
        $sign = trim($exp[1] ?? null);
        $value = trim($exp[2] ?? null);

        preg_match_all(self::REGEX_METHODS, $variable, $methods);

        $variable = null;

        foreach ($methods[1] as $key => $method) {
            if (!$variable) {
                $variable = $this->args[$method];
                continue;
            }

            if ($methods[2][$key]) {
                $variable = $variable->$method();
            } else {
                $variable = $variable->$method;
            }
        }

        if ((string) $variable != $variable) {
            Response::response(new Error(
                status: 500,
                message: Language::get('error_bad_template_variable2')
            ));
        }

        // Inversion
        switch ($inversion) {
            case '!':
                $variable = !$variable;
                break;
            case '!!':
                $variable = !!$variable;
                break;
        }

        if ($value == 'null') {
            $value = null;
        } else if ($value == 'true') {
            $value = true;
        } else if ($value == 'false') {
            $value = false;
        } else {
            $value = trim($value, "\"'");
        }

        switch ($sign) {
            case '<':
                return $variable < $value;
            case '>':
                return $variable > $value;
            case '<=':
                return $variable <= $value;
            case '>=':
                return $variable >= $value;
            case '!=':
            case '<>':
                return $variable != $value;
            case '!==':
                return $variable !== $value;
            case '==':
                return $variable == $value;
            case '===':
                return $variable === $value;
            default:
                return (bool) $variable;
        }
    }

    protected function getElseCondition(string &$content): bool
    {
        if (preg_match(self::REGEX_ELSE, $content, $matches)) {
            $content = trim($matches[2]);
            return true;
        }

        return false;
    }

    protected function getElseIFCondition(string &$content): bool
    {
        if (preg_match_all(self::REGEX_ELSEIF, $content, $matches)) {
            foreach ($matches[0] as $key => $pattern) {
                if ($this->getIfCondition(trim($matches[2][$key]))) {
                    $content = trim($matches[3][$key]);
                    return true;
                } else {
                    $content = str_replace($pattern, '', $content);
                }
            }
        }

        return false;
    }

    protected function parseIf()
    {
        if (preg_match_all(self::REGEX_IF, $this->content, $matches)) {
            foreach ($matches[0] as $key => $pattern) {
                $content = trim($matches[3][$key]);

                if ($this->getIfCondition(trim($matches[2][$key]))) {
                    $this->content = str_replace(
                        $pattern,
                        $content,
                        $this->content
                    );
                    return $this;
                }


                if ($this->getElseIFCondition($content)) {
                    $this->content = str_replace(
                        $pattern,
                        $content,
                        $this->content
                    );
                    return $this;
                }

                if ($this->getElseCondition($content)) {
                    $this->content = str_replace(
                        $pattern,
                        $content,
                        $this->content
                    );
                    return $this;
                }
            }
        }

        return $this;
    }

    protected function parseForEach()
    {
        if (preg_match_all(self::REGEX_FOREACH, $this->content, $matches)) {
            foreach ($matches[0] as $key => $pattern) {
                $iterableName = substr($matches[2][$key], 1);

                if (!array_key_exists($iterableName, $this->args)) {
                    Response::response(new Error(
                        status: 500,
                        message: Language::get('error_bad_template_variable3')
                            . ": {$iterableName}"
                    ));
                }

                $body = '';

                foreach ($this->args[$iterableName] as $obj) {
                    $body .= $this->parseVariables(
                        [substr($matches[3][$key], 1) => $obj],
                        $matches[4][$key]
                    );
                }

                $this->content = str_replace($pattern, $body, $this->content);
            }
        }

        return $this;
    }

    protected function parseTitle()
    {
        if (preg_match_all(self::REGEX_TITLE, $this->content, $matches)) {
            $title = getenv('APP_TITLE') ?: 'Atlantis';

            foreach ($matches[0] as $tag) {
                $this->content = str_replace($tag, $title, $this->content);
            }
        }

        return $this;
    }

    protected function parseDate()
    {
        if (preg_match_all(self::REGEX_DATE, $this->content, $matches)) {
            foreach ($matches[1] as $key => $val) {
                $this->content = str_replace(
                    $matches[0][$key],
                    strftime($val),
                    $this->content
                );
            }
        }

        return $this;
    }

    protected function parseCSRF()
    {
        if (preg_match(self::REGEX_CSRF, $this->content)) {
            $this->content = preg_replace(
                self::REGEX_CSRF,
                View::render('csrf', ['csrfToken' => CSRF::get() ?? CSRF::set()]),
                $this->content
            );
        }

        if (preg_match(self::REGEX_CSRF_TOKEN, $this->content)) {
            $this->content = preg_replace(
                self::REGEX_CSRF_TOKEN,
                CSRF::get() ?? CSRF::set(),
                $this->content
            );
        }

        return $this;
    }

    protected function parseEnv()
    {
        if (preg_match_all(self::REGEX_ENV, $this->content, $matches)) {
            foreach ($matches[1] as $key => $val) {
                $this->content = str_replace(
                    $matches[0][$key],
                    getenv($val) ?: '',
                    $this->content
                );
            }
        }

        return $this;
    }

    protected function parseLoad()
    {
        if (preg_match_all(self::REGEX_LOAD, $this->content, $matches)) {
            foreach ($matches[1] as $key => $val) {
                $this->content = str_replace(
                    $matches[0][$key],
                    file_get_contents("./{$val}") ?: '',
                    $this->content
                );
            }
        }

        return $this;
    }

    protected function parseUniqid()
    {
        if (preg_match_all(self::REGEX_UNIQID, $this->content, $matches)) {
            foreach ($matches[1] as $key => $val) {
                $this->content = str_replace(
                    $matches[0][$key],
                    $this->uniqid,
                    $this->content
                );
            }
        }

        return $this;
    }

    protected function parseNonce()
    {
        if (preg_match_all(self::REGEX_NONCE, $this->content, $matches)) {
            foreach ($matches[1] as $key => $val) {
                $this->content = str_replace(
                    $matches[0][$key],
                    Session::get('SCRIPT_NONCE'),
                    $this->content
                );
            }
        }

        return $this;
    }
}
