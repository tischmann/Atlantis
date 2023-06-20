<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{
    Category
};

use Exception;

use Tischmann\Atlantis\{
    Alert,
    Breadcrumb,
    Controller,
    CSRF,
    Locale,
    Request,
    Response,
    View
};

class LocalesController extends Controller
{
    /**
     * Вывод главной страницы админпанели
     */
    public function index(Request $request)
    {
        $this->checkAdmin();

        View::send(
            'admin/locales',
            [
                'locales' => Locale::available(),
            ]
        );
    }

    /**
     * Форма добавления локали
     *
     * @param Request $request
     * 
     * @return void
     */
    public function new(Request $request)
    {
        $this->checkAdmin();

        $this->editor();
    }

    /**
     * Вывод формы добавления/редактирования локали
     * 
     * @param Category $category Категория
     */
    protected function editor(string $locale = '')
    {
        $this->checkAdmin();

        $title = Locale::get("locale_" . ($locale ? $locale : 'new'));

        static::setTitle($title);

        View::send(
            'admin/locale',
            [
                'locale' => $locale,
                'title' => $locale ? $title : '',
                'strings' => $locale ? Locale::getLocale($locale) : ['' => ''],

            ]
        );
    }

    /**
     * Добавление локали
     * 
     * @param Request $request Запрос
     * 
     * @throws Exception
     */
    public function add(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'title' => ['required', 'string'],
            'code' => ['required', 'string'],
            'keys' => ['required', 'array'],
            'values' => ['required', 'array'],
        ]);

        $code = $request->request('code');

        $title = $request->request('title');

        $keys = $request->request('keys');

        $values = $request->request('values');

        $file = <<<EOL
        <?php

        return [
            'locale_{$code}' => '$title',
        EOL;

        $file .= PHP_EOL;

        foreach ($keys as $key => $value) {
            if (empty($value)) continue;

            $file .= <<<EOL
                '{$value}' => '{$values[$key]}',
            EOL;

            $file .= PHP_EOL;
        }

        $file .= <<<EOL
        ];
        EOL;

        if (!is_dir(getenv('APP_ROOT') . '/lang')) {
            mkdir(getenv('APP_ROOT') . '/lang', 0755, true);
        }

        $result = file_put_contents(
            getenv('APP_ROOT') . '/lang/' . $code . '.php',
            $file
        );

        Response::redirect(
            '/' . getenv('APP_LOCALE') . '/admin/locales',
            new Alert(
                status: $result ? -1 : 0,
                message: Locale::get($result ? 'locale_saved' : 'locale_save_error')
            )
        );
    }

    /**
     * Вывод формы редактирования локали
     *
     * @param Request $request
     * 
     * @throws Exception
     */
    public function get(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $code = $request->route('code');

        if (!Locale::exists($code)) {
            throw new Exception(Locale::get('locale_not_found'));
        }

        $this->editor($code);
    }

    /**
     * Редактирование локали
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'keys' => ['required', 'array'],
            'values' => ['required', 'array'],
        ]);

        $code = $request->route('code');

        if (!Locale::exists($code)) {
            throw new Exception(Locale::get('locale_not_found'));
        }

        $keys = $request->request('keys');

        $values = $request->request('values');

        $file = <<<EOL
        <?php

        return [
        EOL;

        $file .= PHP_EOL;

        foreach ($keys as $key => $value) {
            if (empty($value)) continue;

            $file .= <<<EOL
                '{$value}' => '{$values[$key]}',
            EOL;

            $file .= PHP_EOL;
        }

        $file .= <<<EOL
        ];
        EOL;

        if (!is_dir(getenv('APP_ROOT') . '/lang')) {
            mkdir(getenv('APP_ROOT') . '/lang', 0755, true);
        }

        $result = file_put_contents(
            getenv('APP_ROOT') . '/lang/' . $code . '.php',
            $file
        );

        if ($result) Locale::clearCache();

        Response::redirect(
            '/' . getenv('APP_LOCALE') . '/admin/locales',
            new Alert(
                status: $result ? -1 : 0,
                message: Locale::get($result ? 'locale_saved' : 'locale_save_error')
            )
        );
    }

    /**
     * Удаление локали
     *
     * @param Request $request
     *
     */
    public function delete(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $code = $request->route('code');

        if (!Locale::exists($code)) {
            throw new Exception(Locale::get('locale_not_found'));
        }

        $result = unlink(getenv('APP_ROOT') . '/lang/' . $code . '.php');

        Response::send([
            'message' => $result
                ? Locale::get('locale_deleted')
                : Locale::get('locale_delete_error')
        ], $result ? 200 : 500);
    }
}
