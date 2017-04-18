<?php

namespace AltoLabs\Snappic\Controller\Data;

class Store extends AbstractDataAction
{
    public function execute()
    {
        if (!$this->verifyToken()) {
            return $this->renderUnauthorized();
        }

        $store = $this->snappicHelper->getCurrentStore();
        $storeName = $store->getGroup()->getName();

        return $this->jsonFactory->create()->setData([
            'id'                          => (int) $store->getId(),
            'name'                        => $storeName,
            'domain'                      => $this->snappicHelper->getDomain(),
            'iana_timezone'               => $this->getIanaTimezone($store),
            'currency'                    => $store->getBaseCurrencyCode($store),
            'money_with_currency_format'  => $this->getMoneyWithCurrencyFormat($store)
        ]);
    }
}
