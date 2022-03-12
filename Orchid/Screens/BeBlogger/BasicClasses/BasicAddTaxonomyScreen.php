<?php

namespace App\Orchid\Screens\BeBlogger\BasicClasses;

use WebXID\BeBlogger\Helpers\ConvertString;
use WebXID\BeBlogger\Document\Taxonomy;
use App\Orchid\Layouts\Course\Basic\AddTaxonomyTable;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Toast;

abstract class BasicAddTaxonomyScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Додати';

    protected $route = 'platform.posts.taxonomy';
    /** @var Taxonomy */
    protected $model = Taxonomy::class;

    #region Extended

    /**
     * Query data.
     *
     * @return array
     */
    public function query($taxonomy_id): array
    {
        if ($taxonomy_id) {
            $this->name = 'Редагувати';
        }

        return [
            'taxonomy' => $taxonomy_id
                ? $this->model::find($taxonomy_id)
                : null,
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Button::make(__('Save'))
                ->icon('save')
                ->type(Color::PRIMARY())
                ->method('saveModel'),
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
            AddTaxonomyTable::class
        ];
    }

    #endregion

    #region Actions

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveModel(Request $request)
    {
        if ($request->route()->parameter('taxonomy_id') > 0) {
            return $this->updateModel($request);
        }

        $this->validateModel($request);

        $this->update(new $this->model, $request);

        Toast::info(__('Saved'));

        return redirect()->route($this->route . '.list');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function updateModel(Request $request)
    {
        $taxonomy_id = $request->route()->parameter('taxonomy_id');

        if (!$taxonomy_id) {
            throw new \InvalidArgumentException('taxonomy_id not exists');
        }

        $this->validateModel($request);

        $this->update($this->model::find($taxonomy_id), $request);

        Toast::info(__('Saved'));

        return redirect()->route($this->route . '.list');
    }

    /**
     * @param Request $request
     */
    protected function validateModel(Request $request)
    {
        $request->validate([
            'taxonomy.title' => ['string', 'required'],
            'taxonomy.slug' => ['string', 'nullable'],
            'taxonomy.description' => ['string', 'nullable'],
        ]);
    }

    /**
     * @param Taxonomy $model
     * @param Request $request
     */
    protected function update($model, Request $request)
    {
        $filter_data = $request->get('taxonomy');

        $model->title = $filter_data['title'] ?? null;
        $model->slug = substr($filter_data['slug'] ?? ConvertString::slugify($model->title), 0, 255);
        $model->description = $filter_data['description'] ?? '';

        $model->save();
    }

    #endregion
}
