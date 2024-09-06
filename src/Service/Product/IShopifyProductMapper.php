<?php

namespace SyncShopifyBundle\Service\Product;

use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use SyncShopifyBundle\Model\Product\ShopifyProduct;

#[AutoconfigureTag(name: self::MAPPER_TAG)]
interface IShopifyProductMapper
{
    const MAPPER_TAG = 'shopify_product_mapper';

    public function getMapperServiceKey(): string;

    public function getProductClassId(): string;

    public function getMappedProduct(ShopifyProduct $shopifyProductModel, Concrete $product): ShopifyProduct;
}