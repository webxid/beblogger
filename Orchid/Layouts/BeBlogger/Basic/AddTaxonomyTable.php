<?php

namespace App\Orchid\Layouts\BeBlogger\Basic;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;
use Orchid\Support\Color;

class AddTaxonomyTable extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): array
    {
        return [
            Input::make('taxonomy.title')
                ->type('text')
                ->max(255)
                ->required()
                ->title('Title')
                ->horizontal(),

            Input::make('taxonomy.slug')
                ->mask([
                    'regex' => '[a-zA-Z0-9-._]+',
                ])
                ->type('text')
                ->max(255)
                ->title('Slug')
                ->horizontal()
                ->help('The allowed sympols: <code>a-zA-Z0-9-._</code>'),

            TextArea::make('taxonomy.description')
                ->title('Description')
                ->rows(6)
                ->horizontal(),

            Button::make(__('Save'))
                ->type(Color::PRIMARY())
                ->icon('save')
                ->method('saveModel')
        ];
    }
}
