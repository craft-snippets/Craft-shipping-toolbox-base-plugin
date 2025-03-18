<?php

namespace craftsnippets\baseshippingplugin;
use craft\commerce\Plugin as CommercePlugin;
use craftsnippets\shippingtoolbox\ShippingToolbox;
use yii\base\Component;
use craftsnippets\baseshippingplugin\ShippingServiceInterface;
use craft\elements\Address;
use craft\commerce\elements\Order;

abstract class ShippingServiceBase extends Component implements ShippingServiceInterface
{

    public function getNameForAddress(Address $address): ?string
    {
        $name = null;
        if(!is_null($address->organization)){
            $name = $address->organization;
        }else if(!is_null($address->fullName)){
            $name = $address->fullName;
        }
        return $name;
    }



}