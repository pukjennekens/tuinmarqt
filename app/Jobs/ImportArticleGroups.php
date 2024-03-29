<?php

namespace App\Jobs;

use App\API\TroubleFree;
use App\Models\ProductCategory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportArticleGroups implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * 
     * @param $page The page to import
     * @param $perPage The amount of articles to import per page
     * @return void
     */
    public function __construct(
        public int $page = 1,
        public int $perPage = 50,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug('Importing article main groups and groups', ['page' => $this->page, 'perPage' => $this->perPage]);

        $articleMainGroups = TroubleFree::getArticleMainGroups($this->page, $this->perPage);
        $articleGroups     = TroubleFree::getArticleGroups($this->page, $this->perPage);

        foreach($articleMainGroups['data'] as $articleMainGroup) {
            ProductCategory::updateOrCreate([
                'external_id' => $articleMainGroup['id'],
                'type'        => 'article_main_group',
            ], [
                'name' => $articleMainGroup['name'] ?? '',
            ]);
        }

        foreach($articleGroups['data'] as $articleGroup) {
            ProductCategory::updateOrCreate([
                'external_id' => $articleGroup['id'],
                'type'        => 'article_group',
            ], [
                'name'      => $articleGroup['name'] ?? '',
                'parent_id' => ProductCategory::where('external_id', $articleGroup['mainGroup'])->first()?->id,
            ]);
        }
    }
}
