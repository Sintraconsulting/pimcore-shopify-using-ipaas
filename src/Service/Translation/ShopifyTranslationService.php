<?php

namespace SyncShopifyBundle\Service\Translation;

use Doctrine\DBAL\Connection;
use Exception;
use Pimcore\Model\DataObject\Concrete;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use SyncShopifyBundle\Abstract\AbstractShopifyService;
use SyncShopifyBundle\Exception\IgnoreDataObjectMappingException;
use SyncShopifyBundle\Model\Translation\ShopifyTranslation;
use Throwable;
use Traversable;

class ShopifyTranslationService extends AbstractShopifyService
{
    /** @var IShopifyTranslationMapper[] $translationMappers */
    private array $translationMappers;

    public function __construct(
        Connection      $connection,
        LoggerInterface $logger,
        #[TaggedIterator(tag: IShopifyTranslationMapper::MAPPER_TAG, indexAttribute: 'key')]
        iterable        $translationMappers)
    {
        parent::__construct($connection, $logger);
        $this->translationMappers = $translationMappers instanceof Traversable ? iterator_to_array($translationMappers) : $translationMappers;
    }

    public function getTranslationsToSync(string $mapperServiceKey, int $limit): array
    {
        $mapperService = $this->getMapperService($mapperServiceKey);
        $productClassId = $mapperService->getProductClassId();

        $productIds = $this->getProductIds($productClassId, $mapperServiceKey, $mapperService->getShopifyChannelKey());

        $newModificationDate = null;
        $mappedProducts = [];
        foreach ($productIds as $productId) {
            try {
                $product = Concrete::getById($productId[self::ALIAS_ID], ['force' => true]);
                $shopifyModelArray = $this->getMappedTranslationArray($mapperService, $product);

                if ($this->upsertProductEtag($productId[self::ALIAS_ID], $shopifyModelArray, $mapperServiceKey)) {
                    $mappedProducts[] = $shopifyModelArray;
                }

                $newModificationDate = $productId[self::ALIAS_MOST_RECENT_MODIFICATION_DATE];
                if (count($mappedProducts) == $limit) {
                    break;
                }
            } catch (IgnoreDataObjectMappingException) {
                // Do nothing
            } catch (Throwable $th) {
                $this->logger->error("Error mapping product id: {$productId[self::ALIAS_ID]}, mapper service key: {$mapperServiceKey}, 
                error message: {$th->getMessage()}");
            }
        }

        $this->upsertLastModificationDate($mapperServiceKey, $newModificationDate);

        return $mappedProducts;
    }

    private function getMapperService(string $mapperServiceKey): IShopifyTranslationMapper
    {
        $service = current(array_filter($this->translationMappers, function ($translationMapper) use ($mapperServiceKey) {
            return $translationMapper->getMapperServiceKey() === $mapperServiceKey;
        }));

        if (empty($service)) {
            throw new Exception("Unable to find a mapper service with key equal to '$mapperServiceKey', 
                please check if the service is registered with the correct key");
        }

        return $service;
    }

    private function getMappedTranslationArray(IShopifyTranslationMapper $mapperService, Concrete $product): array
    {
        $shopifyTranslationModel = new ShopifyTranslation();
        $shopifyTranslationModel = $mapperService->getMappedTranslation($shopifyTranslationModel, $product);
        return $shopifyTranslationModel->getAsArray();
    }
}