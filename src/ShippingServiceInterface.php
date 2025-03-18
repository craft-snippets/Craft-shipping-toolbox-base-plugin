<?php

namespace craftsnippets\baseshippingplugin;
use craft\commerce\elements\Order;
use craft\elements\Address;

interface ShippingServiceInterface
{
    public static function getPlugin();
    public function createShipmentDetails(Order $order, $requestSettings);
    public function removeShipmentDetails(Order $order, $shipmentDetails);
    public function updateParcelsStatus(Order $order, $pluginHandle);
    public function validateAddress(Address $address, bool $isDelivery);
}