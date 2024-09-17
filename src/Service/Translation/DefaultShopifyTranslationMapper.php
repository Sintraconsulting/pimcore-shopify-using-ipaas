<?php

namespace SyncShopifyBundle\Service\Translation;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\DefaultProduct;
use SyncShopifyBundle\Model\Translation\ShopifyTranslation;
use SyncShopifyBundle\Model\Translation\ShopifyVariantTranslation;

class DefaultShopifyTranslationMapper implements IShopifyTranslationMapper
{
    const DEFAULT_MAPPER_SERVICE_KEY = 'default_shopify_translation';
    const PRODUCT_CLASS_ID = 'DEFAULT_PROD';

    const LANGUAGES_TO_MAP = ["en", "fr", "de"];

    const SHOPIFY_CHANNEL_KEY = 'shopify_1';

    public function getMapperServiceKey(): string
    {
        return self::DEFAULT_MAPPER_SERVICE_KEY;
    }

    public function getProductClassId(): string
    {
        return self::PRODUCT_CLASS_ID;
    }

    public function getShopifyChannelKey(): string
    {
        return self::SHOPIFY_CHANNEL_KEY;
    }

    public function getMappedTranslation(ShopifyTranslation $shopifyTranslationModel, Concrete $product): ShopifyTranslation
    {
        /** @var DefaultProduct $product */

        $shopifyTranslationModel->setSku($product->getSku());

        foreach (self::LANGUAGES_TO_MAP as $lang) {
            $shopifyTranslationModel->addTitle($product->getName($lang), $lang);
            $shopifyTranslationModel->addDescription($product->getDescription($lang), $lang);
            $shopifyTranslationModel->addMetafield("short_description", $product->getShort_description($lang), $lang);
        }

        /** @var DefaultProduct[] $variants */
        $variants = $product->getChildren([AbstractObject::OBJECT_TYPE_VARIANT])->load();
        if (!empty($variants)) {
            $shopifyTranslationModel->addVariants($this->getVariants($variants));
        }

        return $shopifyTranslationModel;
    }

    /**
     * @param DefaultProduct[] $variants
     * @return ShopifyVariantTranslation[]
     */
    private function getVariants(array $variants): array
    {
        $shopifyTranslationVariants = [];

        foreach ($variants as $variant) {
            $shopifyTranslationVariant = new ShopifyVariantTranslation();
            $shopifyTranslationVariant->setSku($variant->getSku());

            foreach (self::LANGUAGES_TO_MAP as $lang) {
                $shopifyTranslationVariant->addMetafield("short_description", $variant->getShort_description($lang), $lang);
                $shopifyTranslationVariant->addOptionValue("color", $variant->getColor($lang), $lang);
                $shopifyTranslationVariant->addOptionValue("size", $variant->getSize($lang), $lang);
            }

            $shopifyTranslationVariants[] = $shopifyTranslationVariant;
        }

        return $shopifyTranslationVariants;
    }
}