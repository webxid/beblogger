<?php

namespace App\Orchid\Layouts\BeBlogger;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Support\Color;
use WebXID\BeBlogger\Document;

class PostsListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'Post';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('id', 'id'),
            TD::make('title', 'title')
                ->render(function (Document $document) {
                    return Link::make($document->title)
                        ->route('platform.posts.edit', $document->id);
                }),
            TD::make('slug', 'slug')
                ->render(function (Document $document) {
                    return Link::make(substr($document->slug, 0, 70))
                        ->route('platform.posts.edit', $document->id);
                }),
            TD::make('is_published', 'is_published')
                ->width('130px')
                ->render(function (Document $document) {
                    return $document->isPublic() ? 'Publish' : 'Ні';
                }),
            TD::make('posted_at', 'posted_at')
                ->width('100px')
                ->render(function (Document $document) {
                    return $document->posted_at->format('Y-m-d');
                }),

            TD::make()
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (Document $document) {
                    return Button::make()
                        ->icon('trash')
                        ->type(Color::DANGER())
                        ->confirm(__('beblogger.admin.data_deletes_permanently'))
                        ->method('removeModel', [
                            'course_id' => $document->id,
                        ]);
                }),
        ];
    }
}
