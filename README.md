The lib contain a basic models and migration things to build a blog

# Install

1. Run `composer require webxid/beblogger`
2. Create a Models extanded from `WebXID\BeBlogger\Document::class` and `WebXID\BeBlogger\Taxonomy::class` 
3. Copy [migration files](./database/migrations/2021_08_17_224023_create_blog_etc_posts_table.php) and configure it
4. Run `php artisan migration`
5. If you use [Orchid admin panel](https://orchid.software/)
    - Copy Orchid and create reuire Orchid Screens for your models, which you created at Step 2.
    - Copy routing settiing fron [platform](./routes/platform.php) to your project `routes/platform.php` file and update the routes for your each model 