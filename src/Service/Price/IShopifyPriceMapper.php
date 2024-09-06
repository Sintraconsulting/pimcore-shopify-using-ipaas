<?php

namespace SyncShopifyBundle\Service\Price;

use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use SyncShopifyBundle\Model\Price\ShopifyPrice;

#[AutoconfigureTag(name: self::MAPPER_TAG)]
interface IShopifyPriceMapper
{
    const MAPPER_TAG = 'shopify_price_mapper';

    public function getMapperServiceKey(): string;

    public function getProductClassId(): string;

    public function getMappedPrice(ShopifyPrice $shopifyPriceModel, Concrete $product): ShopifyPrice;
}