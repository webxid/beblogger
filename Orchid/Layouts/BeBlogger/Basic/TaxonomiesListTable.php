<?php

namespace App\Orchid\Layouts\BeBlogger\Basic;

use WebXID\BeBlogger\Document\Taxonomy;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Support\Color;

class TaxonomiesListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'taxonomy';
    protected $route = '';

    protected function columns(): array
    {
        $prefix = $this->route;

        return [
            TD::make('id', 'id')
                ->align(TD::ALIGN_CENTER)
                ->width('50px'),
            TD::make('title', 'title')
                ->render(function ($taxonomy) use ($prefix) {
                    /** @var Taxonomy $taxonomy */
                    return Link::make($taxonomy->title)
                        ->route($prefix . '.edit', $taxonomy->id);
                }),
            TD::make('slug', 'slug')
                ->render(function ($taxonomy) use ($prefix) {
                    /** @var Taxonomy $taxonomy */
                    return Link::make(substr($taxonomy->slug, 0, 70))
                        ->route($prefix . '.edit', $taxonomy->id);
                }),

            TD::make()
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function ($taxonomy) {
                    /** @var Taxonomy $taxonomy */
                    return Button::make()
                        ->icon('trash')
                        ->type(Color::DANGER())
                        ->confirm(__('beblogger.admin.data_deletes_permanently'))
                        ->method('removeModel', [
                            'taxonomy_id' => $taxonomy->id,
                        ]);
                }),
        ];
    }
}
