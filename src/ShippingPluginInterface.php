<?php

namespace craftsnippets\baseshippingplugin;

use craft\commerce\elements\Order;
use craftsnippets\baseshippingplugin\ShippingServiceBase;

interface ShippingPluginInterface
{
    public static function getSettingsTemplate(): string;
    public static function getSettingsClass(): string;
    public static function getShipmentDetailsClass(): string;
    public static function getShippingName(): string;
    public function isAllowedForOrder(Order $order): bool;

//    public function hasCorrectSettings(): bool;

    public static function getLabelFolderName(): string;
    public function getPluginService(): ShippingServiceBase;
}