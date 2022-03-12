<?php

namespace App\Orchid\Screens\BeBlogger;

use WebXID\BeBlogger\Helpers\ConvertString;
use WebXID\BeBlogger\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use InvalidArgumentException;

class AddPostScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Add Post';
    private ?Document $post = null;

    /**
     * Query data.
     *
     * @return array
     */
    public function query($course_id = null): array
    {
        if ($course_id) {
            $this->name = 'Edit Post';
            $this->post = Document::find($course_id);
        }

        return [
            'course' => $this->post,
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
            Layout::rows([
                    Input::make('post.title')
                        ->type('text')
                        ->max(255)
                        ->required()
                        ->title('Title')
                        ->horizontal(),
                    Input::make('post.slug')
                        ->mask([
                            'regex' => '[a-zA-Z0-9-._]+',
                        ])
                        ->type('text')
                        ->max(255)
                        ->title('Slug')
                        ->horizontal()
                        ->help('Allowed symbols: <code>a-zA-Z0-9-._</code>'),
//
//                    DateTimer::make('post.start_date')
//                        ->title('Початок курсу')
//                        ->required()
//                        ->format('Y-m-d')
//                        ->horizontal()
//                        ->allowInput(),

//                    Input::make('post.back_link')
//                        ->title('Referral link')
//                        ->required()
//                        ->placeholder('http://')
//                        ->type('url')
//                        ->horizontal()
//                        ->help('Referral link'),

//                    Input::make('post.price')
//                        ->title('Price')
//                        ->type('numeric')
//                        ->horizontal()
//                        ->help('в грн.'),

                    Quill::make('post.text')
                        ->required()
                        ->title('Text'),
                ])
                ->title('Main detail'),

            Layout::rows([
                    Select::make('post.taxonomies')
                        ->required()
                        ->title('Taxonomy')
                        ->fromModel(Document\Taxonomy::class, 'title')
                        ->empty('---')
                        ->horizontal(),
                ])
                ->title('Taxonomies'),

            Layout::rows([
                    DateTimer::make('post.posted_at')
                        ->title('Publish date')
                        ->horizontal()
                        ->placeholder('Now')
                        ->enableTime(),

                    Switcher::make('post.is_published')
                        ->sendTrueOrFalse()
                        ->placeholder('Public')
                        ->value(true)
                        ->help('Uncheck this checkbox to save as draft'),

                    Button::make(__('Save'))
                        ->type(Color::PRIMARY())
                        ->icon('save')
                        ->method('saveModel'),
                ])
                ->title('Post'),
        ];
    }

    #region Actions

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function saveModel(Request $request)
    {
//        $post = $request->get('post');

//        if ($post['price'] ?? false) {
//            $request->merge([
//                'post' => [
//                    'price' => number_format(
//                           str_replace(',', '', $post['price']),
//                           2,
//                           '.',
//                           ''
//                        ),
//                    ] + $post,
//                ]);
//        }

        $request->validate([
            'post.title' => ['string', 'required'],
            'post.slug' => ['string', 'nullable'],
//            'post.start_date' => ['date_format:Y-m-d', 'required'],
//            'post.back_link' => ['string', 'required'],
//            'post.price' => ['numeric', 'nullable'],

            'post.taxonomies' => ['array', 'nullable'],
            'post.taxonomies.*' => ['integer', 'nullable'],

            'post.text' => ['string', 'required'],
            'post.posted_at' => ['date_format:Y-m-d H:i:s', 'nullable'],
            'post.is_published' => ['bool'],
        ]);

        if ($request->route()->parameter('post_id') > 0) {
            return $this->updateModel($request);
        }

        $post = $request->get('post');

        /** @var Document $post */
        $post = Document::create([
            'title' => $post['title'],
            'slug' => ($post['slug'] ?? '') ?: ConvertString::slugify($post['title']),
//            'start_date' => $post['start_date'],
//            'price' => $post['price'],
//            'back_link' => $post['back_link'],
            'text' => $post['text'],
            'posted_at' => $post['posted_at'] ?: date('Y-m-d H:i:s'),
            'user_id' => Auth::id(),
            'is_published' => $post['is_published'],
        ]);

        isset($post['taxonomies'])
            && $post->taxonomy()->attach($post['taxonomies']); // replace the method

        Toast::info(__('Saved'));

        return redirect()->route('platform.posts.list');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function updateModel(Request $request)
    {
        $post_id = $request->route()->parameter('post_id');

        if ($post_id < 1) {
            throw new InvalidArgumentException('Invalid $post_id');
        }

        $post_data = $request->get('post');

        /** @var Document $post */
        $post = Document::find($post_id);

        $post->title = $post_data['title'];
        $post->slug = ($post_data['slug'] ?? '') ?: ConvertString::slugify($post_data['title']);
//        $post->start_date = $post_data['start_date'];
//        $post->price = $post_data['price'];
//        $post->back_link = $post_data['back_link'];
        $post->text = $post_data['text'];
        $post->posted_at = $post_data['posted_at'] ?: date('Y-m-d H:i:s');
        $post->user_id = Auth::id();
        $post->is_published = $post_data['is_published'];

        $post->save();
        $post->removeRelatedTaxonomies();

        isset($post_data['taxonomies'])
            && $post->taxonomy()->attach($post_data['taxonomies']); // replace the method

        Toast::info(__('Saved'));

        return redirect()->route('platform.posts.list');
    }

    #endregion
}
