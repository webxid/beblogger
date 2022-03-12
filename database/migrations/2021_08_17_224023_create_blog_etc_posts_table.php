<?php

use Illuminate\Database\Migrations\Migration;
use WebXID\BeBlogger\DB\Migration as BloggerMigration;

/**
 * Class CreateBlogEtcPostsTable.
 */
class CreateBlogEtcPostsTable extends Migration
{
    /**
     * Initial DB table setup for blog etc package.
     */
    public function up(): void
    {
        BloggerMigration::make([
                'documents' => 'posts',
                'document_taxonomies' => 'post_categories',
                'document_to_taxonomy' => 'post_to_category',
                'uploaded_images' => false,
                'document_comments' => false,
            ])
            ->migrationsUp();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        BloggerMigration::make([
                'documents' => 'posts',
                'document_taxonomies' => 'post_categories',
                'document_to_taxonomy' => 'post_to_category',
                'uploaded_images' => false,
                'document_comments' => false,
            ])
            ->migrationsDown();
    }
}
