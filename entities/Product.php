<?php

namespace app\entities;

use DateTime;
use DateTimeZone;
use DomainException;
use TypeError;
use yii\db\ActiveRecord;
use Decimal\Decimal;

/**
 * @property string $name
 * @property string $description
 * @property string $code
 * @property DateTime|null $discontinuedDate in timezone UTC
 * @property int $stockLevel
 * @property Decimal $price in GBP currency
 *
 * @property-read int|null intProductDataId null if record didn't save to DB yet
 * @property-read string strProductName
 * @property-read string strProductDesc
 * @property-read string strProductCode
 * @property-read string|null dtmAdded in timezone UTC
 * @property-read string|null dtmDiscontinued
 * @property-read int intStockLevel
 * @property-read string decPrice in GBP currency
 */
class Product extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public function getPrimaryKey($asArray = false): string
    {
        return 'intProductDataId';
    }

    /**
     * @inheritDoc
     */
    public static function tableName(): string
    {
        return 'tblProductData';
    }

    public function rules(): array
    {
        return [
            [['strProductName', 'strProductDesc', 'strProductCode', 'intStockLevel', 'decPrice'], 'required'],
            ['strProductName', 'string', 'max' => 50],
            ['strProductDesc', 'string', 'max' => 255],
            ['strProductCode', 'string', 'max' => 10],
            ['intStockLevel', 'integer', 'min' => 0],
            ['decPrice', 'validatePrice'],
        ];
    }

    public function validatePrice($attribute): void
    {
        $value = $this->$attribute;
        try {
            $valueDec = new Decimal($value);
        } catch (TypeError|DomainException $e) {
            $this->addError($attribute, $e->getMessage());
            return;
        }

        if ($valueDec < 0) {
            $this->addError($attribute, "Price '$valueDec' can't be less zero.");
        }
    }

    public static function hasByCode(string $code): bool
    {
        return !! self::findOne(['strProductCode' => $code]);
    }

    public function setId(int $value): void
    {
        $this->intProductDataId = $value;
    }

    public function getId(): ?int
    {
        return $this->intProductDataId;
    }

    public function setName(string $value): void
    {
        $this->strProductName = $value;
    }

    public function getName(): string
    {
        return $this->strProductName;
    }

    public function setDescription(string $value): void
    {
        $this->strProductDesc = $value;
    }

    public function getDescription(): string
    {
        return $this->strProductDesc;
    }

    public function setCode(string $value): void
    {
        $this->strProductCode = $value;
    }

    public function getCode(): string
    {
        return $this->strProductCode;
    }

    public function setDiscontinuedDate(?DateTime $value): void
    {
        $this->dtmDiscontinued = $value ? $value->format('Y-m-d H:i:s') : null;
    }

    public function getDiscontinuedDate(): ?DateTime
    {
        return $this->dtmDiscontinued
            ? DateTime::createFromFormat('Y-m-d H:i:s', $this->dtmDiscontinued, new DateTimeZone('UTC'))
            : null;
    }

    public function setStockLevel(int $value): void
    {
        $this->intStockLevel = $value;
    }

    public function getStockLevel(): int
    {
        return $this->intStockLevel;
    }

    public function setPrice(Decimal $value): void
    {
        $this->decPrice = $value->toString();
    }

    public function getPrice(): Decimal
    {
        return new Decimal($this->decPrice);
    }
}