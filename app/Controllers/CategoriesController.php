<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Categories;
use App\Models\{Category};

use Exception;

use Tischmann\Atlantis\{
    Breadcrumb,
    Controller,
    CSRF,
    Locale,
    Request,
    Response,
    View
};

class CategoriesController extends Controller
{
    use CategoriesTrait;

    public function index(Request $request): void
    {
        $this->checkAdmin();

        $locale = $request->route('locale');

        $locales = [];

        $categories = Category::fill(Category::query()->where('parent_id', null)->order('position', 'ASC'));

        foreach ($categories as $category) {
            assert($category instanceof Category);

            if (!array_key_exists($category->locale, $locales)) {
                $locales[$category->locale] = [];
            }

            $locales[$category->locale][] = $category;
        }

        Response::send(
            View::make(
                'admin/categories',
                [
                    'locales' => $locales,
                    'breadcrumbs' => [
                        new Breadcrumb(url: '/admin', label: Locale::get('adminpanel')),
                        new Breadcrumb(label: Locale::get('adminpanel_categories')),
                    ],
                ]
            )->render()
        );

        exit;
    }

    public function getCategory(Request $request): void
    {
        $this->checkAdmin();

        $request->validate([
            'id' => ['required'],
        ]);

        $id = intval($request->route('id'));

        $category = Category::find($id);

        assert($category instanceof Category);

        if (!$category->id) {
            throw new Exception("Category ID:{$id} not found");
        }

        $childrenID = array_keys($category->getAllChildren());

        $parents = Category::fill(
            Category::query()
                ->where('id', '!()', [
                    $category->id,
                    ...$childrenID
                ])
                ->order('position', 'ASC')
        );

        $parents = [new Category(), ...$parents];

        Response::send(
            View::make(
                'admin/category',
                [
                    'category' => $category,
                    'parents' => $parents,
                    'locales' => Locale::available(),
                    'breadcrumbs' => [
                        new Breadcrumb(url: '/admin', label: Locale::get('adminpanel')),
                        new Breadcrumb(url: '/categories', label: Locale::get('adminpanel_categories')),
                        new Breadcrumb(label: $category->title),
                    ],
                ]
            )->render()
        );
    }

    public function orderCategories(Request $request)
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $categories = $request->request('categories');

        foreach ($categories as $position => $id) {
            $category = Category::find($id);

            assert($category instanceof Category);

            if (!$category->id) {
                throw new Exception("Category ID:{$id} not found");
            }

            $category->position = $position + 1;

            if (!$category->save()) {
                throw new Exception("Category ID:{$id} not saved");
            }
        }

        Response::redirect('/' . getenv('APP_LOCALE') . '/categories');
    }

    public function updateCategory(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'id' => ['required'],
            'title' => ['required'],
            'slug' => ['required'],
            'parent_id' => ['required'],
            'locale' => ['required'],
        ]);

        CSRF::verify($request);

        $id = intval($request->route('id'));

        $category = Category::find($id);

        assert($category instanceof Category);

        if (!$category->id) {
            throw new Exception("Category ID:{$id} not found");
        }

        $title = strval($request->request('title'));

        $slug = strval($request->request('slug'));

        $locale = strval($request->request('locale'));

        $parent_id = intval($request->request('parent_id'));

        $children = $request->request('children');

        if (is_array($children)) {
            foreach ($children as $position => $childId) {
                $child = Category::find($childId);

                assert($child instanceof Category);

                if (!$child->id) {
                    throw new Exception("Category ID:{$childId} not found");
                }

                $child->position = $position + 1;

                if (!$child->save()) {
                    throw new Exception("Category ID:{$childId} not saved");
                }
            }
        }

        $category->title = $title;

        $category->slug = $slug;

        $category->locale = $locale;

        $category->parent_id = $parent_id;

        if (!$category->save()) {
            throw new Exception("Category ID:{$id} not saved");
        }

        Response::redirect('/' . getenv('APP_LOCALE') . '/categories');
    }
}
