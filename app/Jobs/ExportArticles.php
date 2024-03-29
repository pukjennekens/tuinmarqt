<?php

namespace App\Jobs;

use App\API\WooCommerce;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExportArticles implements ShouldQueue
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
        public int $perPage = 10,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $woocommerce = WooCommerce::getClient();
        $articles    = Product::paginate($this->perPage, ['*'], 'page', $this->page);

        $woocommerceData = ['create' => [], 'update' => []];

        foreach($articles as $article) {
            /**
             * @var \App\Models\Product $article
             */
            if($article['data']['showInWebshop'] === false) continue;

            if($article->woocommerce_id) {
                $woocommerceData['update'][] = $article->toWooCommerceArray();
            } else {
                $woocommerceData['create'][] = $article->toWooCommerceArray();
            }
        }

        $response = $woocommerce->post('products/batch', $woocommerceData);

        Log::info('ExportArticles', ['response' => $response]);

        if(isset($response->create)) {
            foreach($response->create as $index => $product) {
                $articles[$index]->update(['woocommerce_id' => $product->id]);
            }
        }

        // if($articles->hasMorePages()) {
        //     ExportArticles::dispatch($this->page + 1, $this->perPage);
        // }
    }
}
