<?php

namespace App\Orchid\Screens\BeBlogger;

use App\Orchid\Layouts\BeBlogger\PostsListTable;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use WebXID\BeBlogger\Document;

class PostsListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Posts list';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'Course' => Document::all(),
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
//            ModalToggle::make('Import')
//                ->modal('exampleModal')
//                ->method('importPosts')
//                ->icon('cloud-upload'),

            Link::make('Add Post')
                ->icon('plus')
                ->route('platform.posts.add'),
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
            PostsListTable::class,

            Layout::modal('exampleModal', [
                Layout::rows([
                    Upload::make('csv_file')
                        ->title('All files')
                ]),
            ]),
        ];
    }

    #region Actions

    /**
     * @param Request $request
     */
    public function removeModel(Request $request)
    {
        $course_id = $request->get('course_id');

        if ($course_id < 1) {
            throw new \InvalidArgumentException('Invalid $course_id');
        }

        /** @var Document $post */
        $post = Document::find($course_id);

        $post->delete();

        Toast::info(__('Deleted successfully'));
    }

    /**
     * @param Request $request
     */
    public function importPosts(Request $request)
    {
        dd($request);
    }

    #endregion
}
