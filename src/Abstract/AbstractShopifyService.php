<?php

namespace SyncShopifyBundle\Abstract;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class AbstractShopifyService
{
    const ETAG_SUFFIX = '_etag';
    const LAST_MODIFICATION_DATE_SUFFIX = '_last_modification_date';

    public function __construct(
        protected Connection      $connection,
        protected LoggerInterface $logger)
    {
    }

    protected function upsertLastModificationDate(string $mapperServiceKey, ?int $newModificationDate): void
    {
        if (!is_null($newModificationDate)) {
            $this->connection->executeQuery("
            INSERT INTO settings_store (id, scope, data, type) 
            VALUES( ?, 'ShopifySyncBundle', ?, 'int')
            ON DUPLICATE KEY UPDATE  data = ?
        ", [
                $this->getSettingsStoreKey($mapperServiceKey),
                $newModificationDate,
                $newModificationDate
            ]);
        }
    }

    private function getSettingsStoreKey(string $mapperServiceKey): string
    {
        return $mapperServiceKey . self::LAST_MODIFICATION_DATE_SUFFIX;
    }

    protected function upsertProductEtag(int $productId, array $shopifyModel, string $mapperServiceKey): bool
    {
        $oldEtag = $this->getOldProductEtag($productId, $mapperServiceKey);
        $newEtag = $this->getProductEtag($shopifyModel);

        if ($this->isProductEtagChanged($oldEtag, $newEtag)) {
            if (empty($oldEtag)) {
                $this->connection->executeQuery("
                INSERT INTO notes (type, cid, ctype, date, user, title, description)
                    VALUES( 'content', ?, 'object', unix_timestamp(), 0, ?, ?)
                ", [$productId, $this->getEtagKey($mapperServiceKey), $newEtag]);
            } else {
                $this->connection->executeQuery("
                UPDATE notes 
                SET description = ?, date = unix_timestamp() 
                WHERE cid = ? AND title = ?
                ", [$newEtag, $productId, $this->getEtagKey($mapperServiceKey)]);
            }

            return true;
        } else {
            return false;
        }
    }

    private function getOldProductEtag(int $productId, string $mapperServiceKey): mixed
    {
        return $this->connection->fetchOne("
            SELECT description
            FROM notes
            WHERE cid = ? AND title = ?
        ", [$productId, $this->getEtagKey($mapperServiceKey)]);
    }

    private function getEtagKey(string $mapperServiceKey): string
    {
        return $mapperServiceKey . self::ETAG_SUFFIX;
    }

    private function getProductEtag(array $product): string
    {
        return md5(json_encode($product));
    }

    private function isProductEtagChanged(mixed $oldEtag, string $newEtag): bool
    {
        return $oldEtag != $newEtag || empty($oldEtag);
    }

    protected function getProductIds(string $productClassId, string $mapperServiceKey): array
    {
        $lastModificationDate = $this->getLastModificationDate($mapperServiceKey);

        return $this->connection->fetchAllAssociative("
            SELECT obj.id, MAX(GREATEST(obj.modificationDate, var.modificationDate)) AS mostRecentModificationDate
            FROM objects obj
            INNER JOIN objects var ON obj.id = var.parentId
            WHERE obj.classId = ?
                AND obj.type = 'object'
                AND (var.modificationDate > ? OR obj.modificationDate > ?)
            GROUP BY obj.id
            ORDER BY mostRecentModificationDate ASC
            LIMIT 5000;
        ", [
            $productClassId,
            $lastModificationDate,
            $lastModificationDate
        ]);
    }

    protected function getLastModificationDate(string $mapperServiceKey): int
    {
        $result = $this->connection->fetchOne("
            SELECT data
            FROM settings_store
            WHERE scope = 'ShopifySyncBundle' AND id = ?
        ", [$this->getSettingsStoreKey($mapperServiceKey)]);

        return is_bool($result) ? 0 : $result;
    }

}