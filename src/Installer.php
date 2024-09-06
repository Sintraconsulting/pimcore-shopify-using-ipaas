<?php

namespace SyncShopifyBundle;

use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Service;

class Installer extends AbstractInstaller
{
    const DEFAULT_PRODUCT_FILE = __DIR__ . "/../config/install/class_DefaultProduct.json";

    public function install(): void
    {
        $class = ClassDefinition::getById("DEFAULT_PROD");
        if (empty($class)) {
            $class = ClassDefinition::create([
                "name" => "DefaultProduct",
            ]);

            $json = file_get_contents(self::DEFAULT_PRODUCT_FILE);

            Service::importClassDefinitionFromJson($class, $json);
        }
    }

    public function uninstall(): void
    {
    }

    public function canBeInstalled(): bool
    {
        return true;
    }

    public function canBeUninstalled(): bool
    {
        return true;
    }
}
