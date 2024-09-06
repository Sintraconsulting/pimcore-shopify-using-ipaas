<?php

namespace SyncShopifyBundle\Service\Price;

use Doctrine\DBAL\Connection;
use Exception;
use Pimcore\Model\DataObject\Concrete;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use SyncShopifyBundle\Abstract\AbstractShopifyService;
use SyncShopifyBundle\Model\Price\ShopifyPrice;
use Throwable;
use Traversable;

class ShopifyPriceService extends AbstractShopifyService
{
    /** @var IShopifyPriceMapper[] $productMappers */
    private array $priceMappers;

    public function __construct(
        Connection      $connection,
        LoggerInterface $logger,
        #[TaggedIterator(tag: IShopifyPriceMapper::MAPPER_TAG, indexAttribute: 'key')]
        iterable        $priceMappers)
    {
        parent::__construct($connection, $logger);
        $this->priceMappers = $priceMappers instanceof Traversable ? iterator_to_array($priceMappers) : $priceMappers;
    }

    public function getPricesToSync(string $mapperServiceKey, int $limit): array
    {
        $mapperService = $this->getMapperService($mapperServiceKey);
        $productClassId = $mapperService->getProductClassId();

        $productIds = $this->getProductIds($productClassId, $mapperServiceKey);

        $newModificationDate = null;
        $mappedProducts = [];
        foreach ($productIds as $productId) {
            try {
                $product = Concrete::getById($productId['id'], ['force' => true]);
                $shopifyModelArray = $this->getMappedPriceArray($mapperService, $product);

                if ($this->upsertProductEtag($productId['id'], $shopifyModelArray, $mapperServiceKey)) {
                    $mappedProducts[] = $shopifyModelArray;
                }

                $newModificationDate = $productId['mostRecentModificationDate'];
                if (count($mappedProducts) == $limit) {
                    break;
                }
            } catch (Throwable $th) {
                $this->logger->error("Error mapping product id: {$productId['id']}, mapper service key: {$mapperServiceKey}, 
                error message: {$th->getMessage()}");
            }
        }

        $this->upsertLastModificationDate($mapperServiceKey, $newModificationDate);

        return $mappedProducts;
    }

    private function getMapperService(string $mapperServiceKey): IShopifyPriceMapper
    {
        $service = current(array_filter($this->priceMappers, function ($priceMapper) use ($mapperServiceKey) {
            return $priceMapper->getMapperServiceKey() === $mapperServiceKey;
        }));

        if (empty($service)) {
            throw new Exception("Unable to find a mapper service with key equal to '$mapperServiceKey', 
                please check if the service is registered with the correct key");
        }

        return $service;
    }

    protected function getProductIds(string $productClassId, string $mapperServiceKey): array
    {
        $lastModificationDate = $this->getLastModificationDate($mapperServiceKey);

        return $this->connection->fetchAllAssociative("
            SELECT obj.id, obj.modificationDate AS mostRecentModificationDate
            FROM objects obj
            WHERE obj.classId = ?
                AND obj.type IN  ('object', 'variant')
                AND obj.modificationDate > ?
            ORDER BY mostRecentModificationDate ASC
            LIMIT 5000;
        ", [
            $productClassId,
            $lastModificationDate
        ]);
    }

    private function getMappedPriceArray(IShopifyPriceMapper $mapperService, Concrete $product): array
    {
        $shopifyPriceModel = new ShopifyPrice();
        $shopifyPriceModel = $mapperService->getMappedPrice($shopifyPriceModel, $product);
        return $shopifyPriceModel->getAsArray();
    }
}