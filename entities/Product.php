<?php

namespace app\entities;

use yii\db\ActiveRecord;

/**
 * @property-read int|null intProductDataId null if record didn't save to DB yet
 * @property-read string strProductName
 * @property-read string strProductDesc
 * @property-read string strProductCode
 * @property-read string|null dtmAdded
 * @property-read string|null dtmDiscontinued
 * @property-read string stmTimestamp
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
}