<?php

namespace App\Jobs;

use App\API\TroubleFree;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportArticles implements ShouldQueue
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
        public int $perPage = 100,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug('Importing articles', ['page' => $this->page, 'perPage' => $this->perPage]);

        $articles = TroubleFree::getArticles($this->page, $this->perPage);

        foreach($articles['data'] as $article) {
            $product = Product::updateOrCreate([
                'external_id' => $article['id'],
            ], [
                'name' => $article['description'] ?? '',
                'data' => $article,
            ]);

            $product->images()->delete();

            foreach($article['images'] as $imageId) {
                if(!$imageId) continue;

                $product->images()->create([
                    'external_id' => $imageId,
                ]);
            }
        }

        if($articles['meta']['pagination']['current_page'] < $articles['meta']['pagination']['total_pages']) {
            ImportArticles::dispatch($this->page + 1, $this->perPage);
        }
    }
}
