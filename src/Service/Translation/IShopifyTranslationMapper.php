<?php

namespace SyncShopifyBundle\Service\Translation;

use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use SyncShopifyBundle\Model\Translation\ShopifyTranslation;

#[AutoconfigureTag(name: self::MAPPER_TAG)]
interface IShopifyTranslationMapper
{
    const MAPPER_TAG = 'shopify_translation_mapper';

    public function getMapperServiceKey(): string;

    public function getProductClassId(): string;

    public function getShopifyChannelKey(): string;

    public function getMappedTranslation(ShopifyTranslation $shopifyTranslationModel, Concrete $product): ShopifyTranslation;
}