<?php

namespace craftsnippets\baseshippingplugin;

use Craft;
use craft\base\Plugin;
use craft\commerce\elements\Order;
use craft\elements\Address;
use craft\helpers\UrlHelper;
use craftsnippets\baseshippingplugin\ShipmentDetailsInterface;
use craftsnippets\baseshippingplugin\ShippingPluginInterface;
use craftsnippets\shippingtoolbox\ShippingToolbox;
use craft\base\Model;
use craftsnippets\shippingtoolbox\elements\Shipment;

abstract class ShippingPlugin extends Plugin implements ShippingPluginInterface
{
    public bool $hasCpSettings = true;

    public static function getShippingDetailsClass()
    {

    }
    public function getNotifications(): array
    {
        return [];
    }

    // this can be overridden to include different logo into interface shown on order page
    public function getSvgLogoMarkup(): ?string
    {
        return Craft::$app->plugins->getPluginIconSvg($this->handle);
    }

    public function getSettingsUrl()
    {
        return UrlHelper::cpUrl('settings/plugins/' . $this->handle);
    }

    private function getSettingsFormHtml()
    {
        $formHtml = Craft::$app->view->renderTemplate(static::getSettingsTemplate(), [
            'settings' => $this->getSettings(),
        ]);
        return $formHtml;
    }

    public function getSettingsResponse(): mixed
    {
        // if shipping toolbox missing

        if(!Craft::$app->plugins->isPluginEnabled('shipping-toolbox')){
            $message = '<strong>' . $this->name . '</strong> plugin error - <strong>Shipping Toolbox</strong> plugin is not installed.';
            $title = $this->name . ' plugin error';
            return Craft::$app->controller->renderTemplate('_layouts/cp.twig', [
                'title' => $title,
                'content' => $message,
            ]);
        }

        $title = $this->name;

        // form html

        // for some reason namespacing using twig filter makes selectize stop working
        $formHtml = Craft::$app->getView()->namespaceInputs(function() {
            return (string)$this->getSettingsFormHtml();
        }, 'settings');
        $context = [
            'formHtml' => $formHtml,
            'plugin' => $this,
        ];

        // sidebar
        $sidebarContext = [
            'links' => ShippingToolbox::getInstance()->plugins->getSitebarItems($this->handle),
        ];

        // render
        $screen = Craft::$app->controller->asCpScreen()
            ->title($title)
            ->contentTemplate('shipping-toolbox/settings/settings-base', $context)
            ->pageSidebarTemplate('shipping-toolbox/settings/settings-sidebar', $sidebarContext)
            ->action('plugins/save-plugin-settings')
        ;
        return $screen;
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(static::getSettingsClass());
    }

    public function canIncludeShippingForm($order)
    {
        return true;
    }

    public function renderShippingForm($order)
    {
        $details = $this->getShipmentDetails($order);

        if(is_null($details)){
            return '';
        }

        $template = 'shipping-toolbox/shipping-form.twig';
        $context = [
            'details' => $this->getShipmentDetails($order),
            'locationOptions' => ShippingToolbox::getInstance()->plugins->getLocationOptions(),
            'defaultLocationId' => ShippingToolbox::getInstance()->settings->defaultLocationId,
            'reloadOnRequest' => ShippingToolbox::getInstance()->settings->reloadOnRequest,
        ];
        $html = Craft::$app->view->renderTemplate($template, $context, Craft::$app->view::TEMPLATE_MODE_CP);
        return $html;
    }

    public function getShipmentDetails($order)
    {

        // search for db content
        $propertiesJson = null;
        $shipmentElement = Shipment::find()->orderId($order->id)->one();
        if(!is_null($shipmentElement)){
            $propertiesJson = $shipmentElement->propertiesJson;
        }

        // if it refers to not installed plugin
        if(!is_null($shipmentElement) && $this->handle != $shipmentElement->pluginHandle){
            return null;
        }

        $class = static::getShipmentDetailsClass();
        $obj = new $class([
            'order' => $order,
            'plugin' => $this,
            'jsonData' => $propertiesJson,
            'shipmentElement' => $shipmentElement,
        ]);



//        if($obj instanceof ShipmentDetailsInterface == false){
//            throw new \Exception($this->name . ' - shipment details must implement ShipmentDetailsInterface interface');
//        }
        return $obj;
    }

    public function getPluginLabel()
    {
        return static::getShippingName() . ' ' . Craft::t('shipping-toolbox','shipping');
    }

    // can show inputs
    public function useInputNumberOfParcels()
    {
        return true;
    }

    public function useInputSenderAddress()
    {
        return true;
    }

    public function useInputParcelInfo()
    {
        return true;
    }

    public function useInputPickupDate()
    {
        return false;
    }

    public function useInputWeight()
    {
        return false;
    }

    ////////////
    ///
    public function getSettingsErrors()
    {
        return [];
    }

    public function hasCorrectSettings()
    {
        // can be override to add checks, by default does not throw errors
        // todo also check shipping toolbox settings

        if(!empty($this->getSettingsErrors())){
            return false;
        }
        return true;
    }

    public function canUseForOrder(Order $order)
    {
        // todo
        return true;
    }

//    public static function validateSenderAddress(Address $address): void
//    {
//
//    }
//
//    public static function validateRecipientAddress(Address $address): void
//    {
//
//    }

    // COD disabled by default
    public function canUseCod(Order $order): bool
    {
        return false;
    }

    public function getCodBeforeRequest($order)
    {
        return $order->getTotalPrice();
    }

    public function getCreateParcelsActionClass()
    {
        return null;
    }

    public function senderAddressRequired()
    {
        return false;
    }

    public function updateImmediatelyAfterCreation()
    {
        return true;
    }

    public function supportsParcelShops()
    {
        return false;
    }

    public function supportsCod()
    {
        return false;
    }

    public function getParcelShopsParametersErrors()
    {
        return null;
    }

    public function parcelShopAllowedForOrder(Order $order)
    {
        return false;
    }

    public function getParcelShopSelectWidgetTemplate(): ?string
    {
        return null;
    }

    public static function getShipmentInfContentsClass()
    {
        return null;
    }

    public function getWeightInputInstructions(): ?string
    {
        return null;
    }

}