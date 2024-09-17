<?php

namespace SyncShopifyBundle;

use Doctrine\DBAL\Connection;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Service;
use Pimcore\Model\DataObject\Fieldcollection\Definition;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class Installer extends SettingsStoreAwareInstaller
{
    const DEFAULT_PRODUCT_FILE = __DIR__ . "/../config/install/class_DefaultProduct.json";
    const IMAGE_INFO_FILE = __DIR__ . "/../config/install/fieldcollection_ImageInfo.json";

    public function __construct(
        protected BundleInterface $bundle,
        protected Connection      $db
    )
    {
        parent::__construct($bundle);
    }

    public function install(): void
    {
        $this->installFieldCollections();

        $this->installDataObjects();

        parent::install();
    }

    private function installFieldCollections(): void
    {
        $fieldCollection = Definition::getByKey("ImageInfo");
        if (empty($fieldCollection)) {
            $fieldCollection = new Definition();
            $fieldCollection->setKey("ImageInfo");
        }

        $json = file_get_contents(self::IMAGE_INFO_FILE);

        Service::importFieldCollectionFromJson($fieldCollection, $json);
    }

    private function installDataObjects(): void
    {
        $class = ClassDefinition::getById("DEFAULT_PROD");
        if (empty($class)) {
            $class = ClassDefinition::create([
                "name" => "DefaultProduct",
            ]);
        }

        $json = file_get_contents(self::DEFAULT_PRODUCT_FILE);

        Service::importClassDefinitionFromJson($class, $json);
    }
}