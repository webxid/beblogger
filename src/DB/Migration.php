<?php

namespace WebXID\BeBlogger\DB;

use WebXID\BeBlogger\DataContainers\AbstractDataContainer;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 *  HOW TO USE
 * ============
 *
 * BasicMigration::make([
 *      'documents' => 'posts',
 *      'document_taxonomies' => 'categories',
 *      'document_to_taxonomy' => 'course_to_category',
 *      'document_comments' => 'post_comments',
 *      'uploaded_images' => false, // will skipp the create of the table
 * ])
 *  ->migrationsUp()
 *  ->migrationsDown();
 *
 * ============
 *
 * @property string uploaded_images
 * @property string documents
 * @property string document_taxonomies
 * @property string document_to_taxonomy
 * @property string document_comments
 */
class Migration extends AbstractDataContainer
{
    private static $allowed_properties = [
        'uploaded_images',
        'documents',
        'document_taxonomies',
        'document_to_taxonomy',
        'document_comments',
    ];

    #regino Builders

    /**
     * @param array $data
     * [
     *      table_type => table_name,
     *      ...
     * ]
     *
     * @return Migration
     */
    public static function make(array $data = [])
    {
        foreach ($data as $prop_name => $value) {
            if (in_array($prop_name, static::$allowed_properties)) {
                continue;
            }

            throw new \InvalidArgumentException($prop_name . ' does not allowed param');
        }

        return parent::make($data);
    }

    #endregion

    #region Actions
    /**
     * @param bool $check_if_table_exists
     * @param false $make_defaults
     */
    public function migrationsUp(bool $check_if_table_exists = false, $make_defaults = false)
    {
        $builder = $this;

        foreach (static::$allowed_properties as $table_type) {
            if (
                $this->$table_type === false // the table was skipped
                || (!$this->$table_type && !$make_defaults)
            ) {
                continue;
            }

            $table_name = !$this->$table_type
                ? $table_type // set default value
                : $this->$table_type;

            if ($check_if_table_exists && Schema::hasTable($table_name)) {
                continue;
            }

            switch ($table_type) {
                case 'uploaded_images':
                    Schema::create($table_name, static function (Blueprint $table) {
                        $table->bigIncrements('id');
                        $table->text('source')->comment('an image source route');
                        $table->string('title')->nullable();

                        $table->timestamp('created_at')->useCurrent();
                        $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                    });

                    break;

                case 'documents':
                    Schema::create($table_name, static function (Blueprint $table) use ($builder) {
                        $table->bigIncrements('id');

                        $table->string('slug')->unique();
                        $table->string('title');
                        $table->mediumText('text');
                        $table->string('use_view_file')
                            ->nullable()
                            ->comment('If not null, this should refer to a blade file in /views/');
                        $table->boolean('is_published')->default(false);
                        $table->unsignedBigInteger('uploaded_image_id')->nullable();
                        $table->unsignedBigInteger('user_id')->index();

                        $table->dateTime('posted_at')->index()->useCurrent()
                            ->comment('Public posted at time, if this is in future then it wont appear yet');
                        $table->timestamp('created_at')->useCurrent();
                        $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                        if ($builder->uploaded_images) {
                            $table->foreign('uploaded_image_id')->references('id')->on($builder->uploaded_images)->onDelete('cascade');
                        }
                    });

                    break;

                case 'document_taxonomies':
                    Schema::create($table_name, static function (Blueprint $table) {
                        $table->bigIncrements('id');
                        $table->bigInteger('parent_id')->default(0);

                        $table->string('slug')->unique();
                        $table->string('title')->nullable();
                        $table->mediumText('description')->nullable();

                        $table->timestamp('created_at')->useCurrent();
                        $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                    });

                    break;

                case 'document_to_taxonomy':
                    Schema::create($table_name, static function (Blueprint $table) use ($builder) {
                        $table->bigIncrements('id');
                        $table->unsignedBigInteger('document_id')->index();
                        $table->unsignedBigInteger('taxonomy_id')->index();

                        $table->unique(['document_id', 'taxonomy_id']);

                        if ($builder->documents) {
                            $table->foreign('document_id')->references('id')->on($builder->documents)->onDelete('cascade');
                        }

                        if ($builder->document_taxonomies) {
                            $table->foreign('taxonomy_id')->references('id')->on($builder->document_taxonomies)->onDelete('cascade');
                        }
                    });

                    break;

                case 'document_comments':
                    Schema::create($table_name, static function (Blueprint $table) use ($builder) {
                        $table->bigIncrements('id');

                        $table->unsignedInteger('document_id')->index();
                        $table->unsignedBigInteger('user_id')->nullable()->index()->comment('if user was logged in');
                        $table->string('ip')->nullable()->comment('if enabled in the config file');
                        $table->string('author_name')->nullable()->comment('if not logged in');
                        $table->text('comment')->comment('the comment body');
                        $table->boolean('approved')->default(true);
                        $table->string('author_email')->nullable();
                        $table->string('author_website')->nullable();

                        $table->timestamp('created_at')->useCurrent();
                        $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                        if ($builder->documents) {
                            $table->foreign('document_id')->references('id')->on($builder->documents)->onDelete('cascade');
                        }
                    });

                    break;
            }
        }
    }

    /**
     * @param bool $return_default_tables
     */
    public function migrationsDown(bool $make_defaults = false)
    {
        foreach (array_reverse(static::$allowed_properties) as $table_type) {
            if (
                $this->$table_type === false // the table was skipped
                || (!$this->$table_type && !$make_defaults)
            ) {
                continue;
            }

            $table_name = !$this->$table_type
                ? $table_type // set default value
                : $this->$table_type;

            Schema::dropIfExists($table_name);
        }
    }

    #endregion
}
