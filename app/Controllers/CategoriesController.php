<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{Category};

use Tischmann\Atlantis\{
    Controller,
    Response,
    Template
};

/**
 * Контроллер категорий
 */
class CategoriesController extends Controller
{
    public function fetchCategories(): void
    {
        $items = "";

        $query = Category::query()
            ->where('parent_id', null)
            ->where('locale', mb_strtolower($this->route->args('locale')))
            ->order('locale', 'ASC')
            ->order('title', 'ASC');

        $items = Template::html(
            template: 'assets/option_field',
            args: [
                'value' => '',
                'title' => '',
                'class' => ''
            ]
        );

        foreach (Category::all($query) as $cat) {
            assert($cat instanceof Category);

            $items .= Template::html(
                template: 'assets/option_field',
                args: [
                    'value' => $cat->id,
                    'title' => $cat->title,
                    'class' => ''
                ]
            );

            $cat->children = $cat->fetchChildren();

            foreach ($cat->children as $child) {
                assert($child instanceof Category);

                $items .= Template::html(
                    template: 'assets/option_field',
                    args: [
                        'value' => $child->id,
                        'title' => $child->title,
                        'class' => 'pl-8'
                    ]
                );

                $child->children = $child->fetchChildren();

                foreach ($child->children as $grandchild) {
                    assert($grandchild instanceof Category);

                    $items .= Template::html(
                        template: 'assets/option_field',
                        args: [
                            'value' => $grandchild->id,
                            'title' => $grandchild->title,
                            'class' => 'pl-12'
                        ]
                    );
                }
            }
        }

        Response::json(['items' => $items]);
    }
}
