<?php

namespace App\Orchid\Screens\BeBlogger\BasicClasses;

use App\Orchid\Layouts\Course\Basic\TaxonomiesListTable;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;
use WebXID\BeBlogger\Document\Taxonomy;

abstract class BasicTaxonomiesListScreen extends Screen
{
    const PAGINATION_ITEMS_LIMIT = 25;

    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'SchoolssListScreen';

    protected $route = 'platform.posts.taxonomy';
    protected $model = Taxonomy::class;

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Додати')
                ->icon('plus')
                ->route($this->route . '.add'),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            TaxonomiesListTable::class,
        ];
    }

    #region Actions

    /**
     * @param Request $request
     */
    public function removeModel(Request $request)
    {
        $taxonomy_id = $request->get('taxonomy_id');

        if ($taxonomy_id < 1) {
            throw new \InvalidArgumentException('Invalid $taxonomy_id');
        }

        /** @var Taxonomy $taxonomy */
        $taxonomy = $this->model::find($taxonomy_id);

        $taxonomy->delete();

        Toast::info(__('Deleted successfully'));
    }

    #endregion
}
