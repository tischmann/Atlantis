<?php

namespace Atlantis;

use Atlantis\Routing\Router;

final class Template
{
    public string $content = '';
    public string $uniqid = '';
    public array $tags = [];

    function __construct(string $name)
    {
        $this->content = $this->include($name);
        $this->uniqid = uniqid();
    }

    function parse()
    {
        $this->parseHome()
            ->parseLoader()
            ->parseNotAuth()
            ->parseNotAdmin()
            ->parseAuth()
            ->parseAdmin()
            ->parseStrings()
            ->replaceTags()
            ->parseTitle()
            ->parseDate()
            ->parseUniqid()
            ->parseCSRF()
            ->parseUser()
            ->parseEnv()
            ->parseLoad()
            ->parseLangJs()
            ->parseIncludes();
    }

    function render()
    {
        $this->parse();
        return $this->content;
    }

    function include(string $name)
    {
        $path = "../templates/{$name}.tpl";

        if (!file_exists($path)) {
            return "Template '{$name}' not found";
        }

        return file_get_contents($path);
    }

    function set(string $tag, $value)
    {
        if (!array_key_exists($tag, $this->tags)) {
            $this->tags[$tag] = $value;
            return $this;
        }

        $this->tags[$tag] .= $value;

        return $this;
    }

    function parseHome()
    {
        preg_match_all(
            '/\[home\]([\S|\s]*?)\[\/home\]/i',
            $this->content,
            $matches
        );

        foreach ($matches[1] ?? [] as $key => $val) {
            $this->content = str_replace(
                $matches[0][$key],
                App::$router->isHome() ? $val : '',
                $this->content
            );
        }

        return $this;
    }

    function parseLoader()
    {
        preg_match_all('/(\[loader\])/i', $this->content, $matches);

        foreach ($matches[1] ?? [] as $key => $val) {
            $template = new Template('Core/Loader');
            $this->content = str_replace(
                $matches[0][$key],
                $template->render(),
                $this->content
            );
        }

        return $this;
    }

    function parseNotAuth()
    {
        preg_match_all(
            '/\[notauth\]([\S|\s]*?)\[\/notauth\]/i',
            $this->content,
            $matches
        );

        foreach ($matches[1] ?? [] as $key => $val) {
            $this->content = str_replace(
                $matches[0][$key],
                App::$user->signedIn() ? '' : $val,
                $this->content
            );
        }

        return $this;
    }

    function parseNotAdmin()
    {
        preg_match_all(
            '/\[notadmin\]([\S|\s]*?)\[\/notadmin\]/i',
            $this->content,
            $matches
        );

        foreach ($matches[1] ?? [] as $key => $val) {
            $this->content = str_replace(
                $matches[0][$key],
                App::$user->isAdmin() ? '' : $val,
                $this->content
            );
        }

        return $this;
    }

    function parseAuth()
    {
        preg_match_all(
            '/\[auth\]([\S|\s]*?)\[\/auth\]/i',
            $this->content,
            $matches
        );

        foreach ($matches[1] ?? [] as $key => $val) {
            $this->content = str_replace(
                $matches[0][$key],
                App::$user->signedIn() ? $val : '',
                $this->content
            );
        }

        return $this;
    }

    function parseAdmin()
    {
        preg_match_all(
            '/\[admin\]([\S|\s]*?)\[\/admin\]/i',
            $this->content,
            $matches
        );

        foreach ($matches[1] ?? [] as $key => $val) {
            $this->content = str_replace(
                $matches[0][$key],
                App::$user->isAdmin() ? $val : '',
                $this->content
            );
        }

        return $this;
    }

    function parseStrings()
    {
        preg_match_all(
            '/\[lang=([a-z0-9_-]+)\]/',
            $this->content,
            $matches
        );

        foreach ($matches[1] ?? [] as $key => $val) {
            $this->content = str_replace(
                $matches[0][$key],
                App::$lang->get($val),
                $this->content
            );
        }

        return $this;
    }

    function replaceTags()
    {
        foreach ($this->tags as $tag => $val) {
            $this->content = str_replace("{{$tag}}", $val, $this->content);
        }

        return $this;
    }

    function parseTitle()
    {
        preg_match_all('/(\[title\])/i', $this->content, $matches);

        // $key = "title_" . Router::controller();
        // $string = App::$lang->get($key);

        // if ($string != $key) {
        //     $title = $string;

        //     $key = "title_" . Router::controller() . "_" . Router::action();
        //     $string = App::$lang->get($key);

        //     if ($string != $key) {
        //         $title .= " | {$string}";
        //     }

        //     $title .= ' | ' . getenv('APP_TITLE') ?: '';
        // }

        $title = getenv('APP_TITLE') ?: 'Atlantis';

        foreach ($matches[1] ?? [] as $key => $val) {
            $this->content = str_replace(
                $matches[0][$key],
                $title,
                $this->content
            );
        }

        return $this;
    }


    function parseDate()
    {
        preg_match_all('/\[date=(.+)\]/i', $this->content, $matches);

        foreach ($matches[1] ?? [] as $key => $val) {
            $this->content = str_replace(
                $matches[0][$key],
                date($val),
                $this->content
            );
        }

        return $this;
    }

    function parseCSRF()
    {
        preg_match_all('/(\[csrf\])/i', $this->content, $matches);

        foreach ($matches[1] ?? [] as $key => $val) {
            $csrf = CSRF::get() ?? CSRF::set();
            $template = new Template('Core/CSRF');
            $template->set('value', $csrf);
            $this->content = str_replace(
                $matches[0][$key],
                $template->render(),
                $this->content
            );
        }

        preg_match_all('/(\[csrf-token\])/i', $this->content, $matches);

        foreach ($matches[1] ?? [] as $key => $val) {
            $csrf = CSRF::get() ?? CSRF::set();
            $this->content = str_replace(
                $matches[0][$key],
                $csrf,
                $this->content
            );
        }

        return $this;
    }

    function parseUser()
    {
        preg_match_all('/\[user->(\w+)\]/i', $this->content, $matches);

        foreach ($matches[1] ?? [] as $key => $val) {
            $val = property_exists(App::$user, $val) ? App::$user->{$val} : '';
            $this->content = str_replace(
                $matches[0][$key],
                $val,
                $this->content
            );
        }

        return $this;
    }

    function parseEnv()
    {
        preg_match_all('/\[env=([A-Z0-9_-]+)\]/', $this->content, $matches);

        foreach ($matches[1] ?? [] as $key => $val) {
            $this->content = str_replace(
                $matches[0][$key],
                getenv($val) ?: '',
                $this->content
            );
        }

        return $this;
    }

    function parseLoad()
    {
        preg_match_all('/\[load=(.+)\]/', $this->content, $matches);

        foreach ($matches[1] ?? [] as $key => $val) {
            $path = "./{$val}";
            $content = file_exists($path) ? file_get_contents($path) : '';
            $this->content = str_replace(
                $matches[0][$key],
                $content,
                $this->content
            );
        }

        return $this;
    }

    function parseLangJs()
    {
        preg_match_all('/(\[langjs\])/', $this->content, $matches);

        foreach ($matches[1] ?? [] as $key => $val) {
            $script = "<script>const LANG = {";

            foreach (App::$lang->strings as $langkey => $langval) {
                $script .= "{$langkey}: `{$langval}`,";
            }

            $script .= "}</script>";

            $this->content = str_replace(
                $matches[0][$key],
                $script,
                $this->content
            );
        }

        return $this;
    }

    function parseIncludes()
    {
        preg_match_all(
            '/\[include=([\w|\/]+)\.php(?:\?([^\]]+))?\]/i',
            $this->content,
            $matches
        );

        foreach ($matches[0] ?? [] as $key => $match) {
            $args = [];

            foreach (explode('&', $matches[2][$key] ?? '') as $argument) {
                $exp = explode('=', $argument);

                if (count($exp) > 1) {
                    $args[$exp[0]] = $exp[1];
                }
            }

            $this->content = str_replace(
                $match,
                View::include($matches[1][$key], $args),
                $this->content
            );
        }

        return $this;
    }

    function parseUniqid()
    {
        preg_match_all('/(\[uniqid\])/i', $this->content, $matches);

        foreach ($matches[1] ?? [] as $key => $val) {
            $this->content = str_replace(
                $matches[0][$key],
                $this->uniqid,
                $this->content
            );
        }

        return $this;
    }
}
