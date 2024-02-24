<?php

namespace app\entities;

use yii\db\ActiveRecord;
use Decimal\Decimal;

/**
 * @property DateTime|null $discontinuedDate
 * @property-read int|null intProductDataId null if record didn't save to DB yet
 * @property-read string strProductName
 * @property-read string strProductDesc
 * @property-read string strProductCode
 * @property-read string|null dtmAdded
 * @property-read string|null dtmDiscontinued
 * @property-read int intStockLevel
 * @property-read string decPrice
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
            ? DateTime::createFromFormat('Y-m-d H:i:s', $this->dtmDiscontinued)
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