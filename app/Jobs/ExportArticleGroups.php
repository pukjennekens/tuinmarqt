<?php

namespace App\Jobs;

use App\API\WooCommerce;
use App\Models\ProductCategory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportArticleGroups implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $woocommerce = WooCommerce::getClient();

        $articleMainGroups                = ProductCategory::where('type', 'article_main_group')->get();
        $articleMainGroupsWoocommerceData = ['create' => [], 'update' => []];

        foreach($articleMainGroups as $articleMainGroup) {
            /**
             * @var \App\Models\ProductCategory $articleMainGroup
             */
            if($articleMainGroup->woocommerce_id) {
                $articleMainGroupsWoocommerceData['update'][] = $articleMainGroup->toWooCommerceArray();
            } else {
                $articleMainGroupsWoocommerceData['create'][] = $articleMainGroup->toWooCommerceArray();
            }
        }

        $articleMainGroupsResponse = $woocommerce->post('products/categories/batch', $articleMainGroupsWoocommerceData);

        if(isset($articleMainGroupsResponse->create)) {
            foreach($articleMainGroupsResponse->create as $index => $productCategory) {
                $articleMainGroups[$index]->update(['woocommerce_id' => $productCategory->id]);
            }
        }

        $articleSubGroups                = ProductCategory::where('type', 'article_group')->get();
        $articleSubGroupsWoocommerceData = ['create' => [], 'update' => []];

        foreach($articleSubGroups as $articleSubGroup) {
            /**
             * @var \App\Models\ProductCategory $articleSubGroup
             */
            if($articleSubGroup->woocommerce_id) {
                $articleSubGroupsWoocommerceData['update'][] = $articleSubGroup->toWooCommerceArray();
            } else {
                $articleSubGroupsWoocommerceData['create'][] = $articleSubGroup->toWooCommerceArray();
            }
        }

        $articleSubGroupsResponse = $woocommerce->post('products/categories/batch', $articleSubGroupsWoocommerceData);

        if(isset($articleSubGroupsResponse->create)) {
            foreach($articleSubGroupsResponse->create as $index => $productCategory) {
                $articleSubGroups[$index]->update(['woocommerce_id' => $productCategory->id]);
            }
        }
    }
}
