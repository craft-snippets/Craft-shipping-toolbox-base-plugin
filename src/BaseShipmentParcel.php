<?php

namespace craftsnippets\baseshippingplugin;
use craft\base\Model;
use craft\commerce\elements\Order;
use craftsnippets\baseshippingplugin\BaseShipmentParcelInterface;

abstract class BaseShipmentParcel extends Model implements BaseShipmentParcelInterface
{
    public int $number;
    public Order $order;
    public function getTitle()
    {
        return $this->number;
    }
    public function getStatusText()
    {
        return null;
    }
}