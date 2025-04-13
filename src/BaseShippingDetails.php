<?php

namespace craftsnippets\baseshippingplugin;

use Craft;
use craft\base\Model;
use craft\commerce\elements\Order;
use craft\elements\Asset;
use craftsnippets\baseshippingplugin\ShipmentDetailsInterface;
use craftsnippets\shippingtoolbox\ShippingToolbox;


abstract class BaseShippingDetails extends Model implements ShipmentDetailsInterface
{

    public Order $order;

    public $jsonData;
    public $parcels = [];

    public $plugin;

    public $shipmentElement;

    public function getHasParcels()
    {
        return !empty($this->parcels);
    }

    public function createParcelsActionAllowed()
    {
        if($this->getHasParcels() == true){
            return false;
        }
        if($this->getCanUse() == false){
            return false;
        }
        return true;
    }

    public function updateParcelsActionAllowed()
    {
        if($this->getHasParcels() == false){
            return false;
        }
        if($this->getCanUse() == false){
            return false;
        }
        return true;
    }

    public function getCanUse()
    {
        $order = $this->order;
        // don't add to cart page in the control panel
        if($order->isCompleted == false){
            return false;
        }
        if($this->plugin->isAllowedForOrder($order) == false){
            return false;
        }
        return true;
    }


    public function getPdfAsset()
    {
        if(is_null($this->shipmentElement)){
            return null;
        }
        if(!is_numeric($this->shipmentElement->labelAssetId)){
            return null;
        }
        $asset = Asset::find()->id($this->shipmentElement->labelAssetId)->one();
        return $asset;
    }

    public function getLabelActionAllowed()
    {
        if($this->getHasParcels() == false){
            return false;
        }
        if($this->getCanUse() == false){
            return false;
        }
        return true;
    }

    public function getIndexColumnStatusesSummary()
    {
        if(empty($this->parcels)){
            return '';  // element index cannot use null
        }
        $statuses = array_map(function($parcel) {
            return $parcel->getStatusText();
        }, $this->parcels);
        $statuses = array_filter($statuses);
        $statusesText = $this->plugin->getShippingName() . ' - ';
        if(empty($statuses)){
            $statusesText .= '[' . Craft::t('shipping-toolbox', 'No status') . ']';
        }else{
            $statusesText .= implode(', ', $statuses);
        }
        return $statusesText;;
    }

    public function getShippingDetails()
    {
        return [];
    }

    // inputs

    // parcel shop
    public function getParcelShopCodeBeforeCreation()
    {
        return ShippingToolbox::getInstance()->plugins->getParcelShopCodeForOrder($this->order);
    }

    public function hasRequiredParcelShopParams()
    {
        return false;
    }

    // cod
    public function getDefaultCodAmount()
    {
        return $this->order->getTotalPrice();
    }

    public function codIsEnabledBeforeCreation()
    {
        return false;
    }

    public function getCodCurrencyBeforeCreation()
    {
        return null;
    }

    public function getCodAmountNumber()
    {
        return null;
    }

    public function getCodAmountCurrency()
    {
        return null;
    }

    public function codAmountIsDifferentFromTotalPrice(): bool
    {
        if(is_null($this->getCodAmountNumber())){
            return false;
        }
        return $this->getCodAmountNumber() != $this->order->getTotalPrice();
    }

    public function isCodForced(): bool
    {
        return false;
    }

    public function isCodDisabled(): bool
    {
        return false;
    }

    public function infoBeforeCreation(): array
    {
        return [];
    }

}