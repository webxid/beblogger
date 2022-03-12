<?php

namespace WebXID\BeBlogger;

use WebXID\BeBlogger\Document\Taxonomy;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use InvalidArgumentException;
use RuntimeException;

/**
 * @property int id
 * @property string slug
 * @property string title
 * @property string text
 * @property string use_view_file
 * @property bool is_published
 * @property int uploaded_image_id
 * @property int user_id
 * @property \DateTime posted_at
 * @property string created_at
 * @property string updated_at
 */
abstract class Document extends Model
{
    use HasFactory;

    const TABLE_NAME = 'posts';

    protected $table = self::TABLE_NAME;

    /**
     * @var array
     */
    public $casts = [
        'posted_at' => 'datetime',
        'is_published' => 'boolean',
    ];
    /**
     * @var array
     */
    public $dates = [
        'posted_at',
    ];
    /**
     * @var array
     */
    public $fillable = [
        'slug',
        'title',
        'text',
        'use_view_file',
        'is_published',
        'uploaded_image_id',
        'user_id',
        'posted_at',
    ];

    #region Updates methods

    /**
     * @return $this
     */
    abstract public function removeRelatedTaxonomies(): self;

//    /**
//     * @return $this
//     */
//    public function removeRelatedTaxonomies()
//    {
//        DB::table(Taxonomy::TABLE_DOCUMENT_TO_TAXONOMY)->where('document_id', $this->id)->delete();
//
//        return $this;
//    }

    #endregion

    #region Getters

    /**
     * @param Taxonomy $taxonomy_class_name
     *
     * @return BelongsToMany
     */
    final protected function getTaxonomies(string $taxonomy_class_name)
    {
        return $this->belongsToMany(
            $taxonomy_class_name,
            constant($taxonomy_class_name . '::TABLE_DOCUMENT_TO_TAXONOMY'),
            'document_id',
            'taxonomy_id'
        );
    }

//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
//     */
//    public function taxonomy()
//    {
//        return $this->getTaxonomies(Taxonomy::class);
//    }

    #endregion

    /**
     * @inheritDoc
     */
    protected static function boot()
    {
        parent::boot();

//        static::$authorNameResolver = config('blogetc.comments.user_field_for_author_name');

        /* If user is logged in and \Auth::user()->canManageBlogEtcPosts() == true, show any/all posts.
           otherwise (which will be for most users) it should only show published posts that have a posted_at
           time <= Carbon::now(). This sets it up: */
//        static::addGlobalScope(new BlogEtcPublishedScope());
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->is_published && $this->posted_at <= (new \DateTime());
    }

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    /**
     * Returns the public facing URL to view this blog post.
     */
    public function url(): string
    {
        return route('beblogger.single', $this->slug);
    }

    /**
     * The associated author (if user_id) is set.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(config('beblogger.user_model', User::class), 'user_id');
    }

    /**
     * Returns true if the database record indicates that this blog post
     * has a featured image of size $size.
     *
     * @param string $size
     */
    public function hasImage($size = 'medium'): bool
    {
        throw new \LogicException('The method hasImage does not implement correctly');

        $this->checkValidImageSize($size);

        return array_key_exists('image_'.$size, $this->getAttributes()) && $this->{'image_'.$size};
    }

    /**
     * Throws an exception if $size is not valid
     * It should be either 'large','medium','thumbnail'.
     *
     * @throws InvalidArgumentException
     */
    protected function checkValidImageSize(string $size = 'medium'): bool
    {
        if (array_key_exists('image_'.$size, config('beblogger.image_sizes', []))) {
            return true;
        }

        throw new InvalidArgumentException('BeBlogger image size should be \'large\', \'medium\', \'thumbnail\''.' or another field as defined in config/beblogger.php. Provided size ('.e($size).') is not valid');
    }

    /**
     * Get the full URL for an image
     * You should use ::has_image($size) to check if the size is valid.
     *
     * @param string $size - should be 'medium' , 'large' or 'thumbnail'
     */
    public function imageUrl($size = 'medium'): string
    {
        throw new \LogicException('The method hasImage does not implement correctly');

        $this->checkValidImageSize($size);
        $filename = $this->{'image_'.$size};

        return asset(config('beblogger.blog_upload_dir', 'blog_images').'/'.$filename);
//        return UploadsService::publicUrl($filename);
    }

    /**
     * Return author string (either from the User (via ->user_id), or the submitted author_name value.
     *
     * @return string
     */
    public function authorString(): ?string
    {
        throw new \LogicException('The method hasImage does not implement correctly');

        // TODO
//        if ($this->author) {
//            return is_callable(self::$authorNameResolver)
//                ? call_user_func(self::$authorNameResolver, $this->author)
//                : $this->author->{self::$authorNameResolver};
//        }
        if ($this->author) {
            return (string) optional($this->author)->name;
        }

        return 'Unknown Author';
    }

    /**
     * Return the URL for editing the post (used for admin users).
     */
    public function editUrl(): string
    {
        return route('platform.post.edit', $this->id);
    }

    /**
     * If $this->user_view_file is not empty, then it'll return the dot syntax
     * location of the blade file it should look for.
     */
    public function bladeViewFile(): string
    {
        if (!$this->use_view_file) {
            throw new RuntimeException('use_view_file was empty, so cannot use bladeViewFile()');
        }

        return 'custom_blog_posts.'.$this->use_view_file;
    }
}
