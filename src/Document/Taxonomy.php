<?php

namespace WebXID\BeBlogger\Document;

use WebXID\BeBlogger\Document;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * @property int id
 * @property int parent_id
 * @property string slug
 * @property string title
 * @property string description
 * @property string created_at
 * @property string updated_at
 */
abstract class Taxonomy extends Model
{
    use HasFactory;

    const TABLE_NAME = 'document_taxonomies';
    const TABLE_DOCUMENT_TO_TAXONOMY = 'document_to_taxonomy';
    const DOCUMENT_CLASS_NAME = Document::class;

    protected $table = self::TABLE_NAME;

    public $fillable = [
        'parent_id',
        'slug',
        'title',
        'description',
    ];

    #region Updates methods

    /**
     * @inheritDoc
     */
    public function delete()
    {
        DB::table(static::TABLE_DOCUMENT_TO_TAXONOMY)
            ->where('taxonomy_id', $this->id)
            ->delete();

        return parent::delete();
    }

    #endregion

    #regino Getters

    /**
     * @return BelongsToMany
     */
    protected function getDocuments(): BelongsToMany
    {
        return $this->belongsToMany(
            static::DOCUMENT_CLASS_NAME,
            static::TABLE_DOCUMENT_TO_TAXONOMY,
            'taxonomy_id',
            'document_id'
        );
    }

    /**
     * Returns the public facing URL of showing blog posts in this category.
     *
     * @return string
     */
    public function url(): string
    {
        return route('beblogger.view_catalog', $this->slug);
    }

    /**
     * Returns the URL for an admin user to edit this category.
     */
    public function editUrl(): string
    {
        return route('platform.admin.taxonomies.edit_category', $this->id);
    }

    /**
     * Parent relation (self-referential)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    protected function parent() {
        return $this->belongsTo(get_class($this), 'parent_id');
    }

    /**
     * Children relation (self-referential)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    protected function children() {
        return $this->hasMany(get_class($this), 'parent_id');
    }

    #endregion
}
