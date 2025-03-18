<?php

namespace craftsnippets\baseshippingplugin;

interface BaseShipmentParcelInterface
{
    public function getIsDelivered(): bool;
}