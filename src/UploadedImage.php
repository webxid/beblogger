<?php

namespace WebXID\BeBlogger;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property int id
 * @property string[] source
 * @property string title
 * @property string created_at
 * @property string updated_at
 */
class UploadedImage extends Model
{
    public $table = 'uploaded_images';

    public $casts = [
        'source' => 'array',
    ];

    public $fillable = [
        'source',
        'title',
    ];
}
