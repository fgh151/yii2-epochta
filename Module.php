<?php

namespace fgh151\modules\epochta;
use fgh151\modules\epochta\eclasses\Account;
use fgh151\modules\epochta\eclasses\Addressbook;
use fgh151\modules\epochta\eclasses\APISMS;
use fgh151\modules\epochta\eclasses\Stat;
use yii\base\ErrorException;

/**
 * Created by PhpStorm.
 * User: fgorsky
 * Date: 29.01.16
 * Time: 13:53
 */
class Module extends \yii\base\Module
{
    public $sms_key_private='';
    public $sms_key_public='';
    public $URL_GAREWAY='http://atompark.com/api/sms/';
    public $testMode=false;

    private $Gateway = null;
    private $Addressbook = null;
    private $Exceptions = null;
    private $Account = null;
    private $Stat = null;

    /**
     * Инициализация шлюза
     */
    public function init()
    {
        $this->Gateway = new APISMS($this->sms_key_private,$this->sms_key_public, $this->URL_GAREWAY);
        parent::init();
    }

    /**
     * Регистрауия имени отправителя
     * @param $name
     * @param string $country
     * @return bool
     */
    public function registerSender($name, $country = 'ru'){
        return $this->callMethod('Account', [$name, $country]);
    }

    /**
     * Создание адресной книги
     * @param string $name
     * @return mixed
     */
    public function createAddressBook($name)
    {
        return $this->callMethod('Addressbook', 'addAddressBook', $name);
    }

    /**
     * Добавление телефона в адресную книгу
     * @param integer $bookId
     * @param string $phone
     * @param string $name
     * @return mixed
     */
    public function addPhoneToBook($bookId, $phone, $name)
    {
        return $this->callMethod('Addressbook', 'addPhoneToAddressBook', [$bookId, $phone,  $name]);
    }

    /**
     * Возвращает баланс
     * @return mixed
     */
    public function getBalans()
    {
        return $this->callMethod('Account', 'getUserBalance');
    }

    /**
     * Проверка можно лиотправить сообщение по адресатам книги
     * @param string $name
     * @param string $message
     * @param integer $bookId
     * @return mixed
     */
    public function testCampaign($name, $message, $bookId)
    {
        return $this->callMethod('Stat', 'checkCampaignPrice', [$name, $message, $bookId]);
    }

    /**
     * Отправка сообщения
     * @param string $name
     * @param string $message
     * @param integer $bookId
     * @return mixed
     * @throws ErrorException
     */
    public function createCampaign($name, $message, $bookId)
    {
        $balans = $this->getBalans();
        $balans = $balans["result"]["balance_currency"];
        $cost = $this->testCampaign($name, $message, $bookId);
        $cost = $cost["result"]["price"];
        if ($balans < $cost) {
            throw new ErrorException('No money');
        }
        return $this->callMethod('Stat', 'createCampaign', [$name, $message, $bookId, "", 0, 0, 0, ""]);
    }

    /**
     * Проверка статуса
     * @param integer $campaignId
     * @return mixed
     */
    public function getStatus($campaignId)
    {
        return $this->callMethod('Stat', 'getCampaignDeliveryStats', $campaignId);
    }

    /**
     * Метод вызывает функции сущностей
     * @param string $entity
     * @param string $command
     * @param array $params
     * @return mixed
     */
    private function callMethod($entity, $command, $params = [])
    {
        if(!$this->$entity) {
            $this->$entity = new $entity($this->Gateway);
        }
        return $this->$entity->$command(implode(', ',$params));
    }

}