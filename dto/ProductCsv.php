<?php

namespace app\dto;

use yii\base\Model;

class ProductCsv extends Model
{
    const DISCONTINUED_YES = 'yes';
    const DISCONTINUED_NONE = '';

    const DISCONTINUED_VALUES = [
        self::DISCONTINUED_YES,
        self::DISCONTINUED_NONE,
    ];

    public $code;

    public $name;

    public $description;

    public $stock;

    /**
     * in GBP currency
     */
    public $cost;

    public $discontinued;

    public function rules(): array
    {
        return [
            [['name', 'description', 'code', 'stock', 'cost'], 'required'],
            [['name', 'description', 'code'], 'string'],
            ['stock', 'integer'],
            ['cost', 'number'],
            ['discontinued', 'in', 'range' => self::DISCONTINUED_VALUES],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Product Name',
            'description' => 'Product Description',
            'code' => 'Product Code',
            'stock' => 'Stock',
            'cost' => 'Cost in GBP',
            'discontinued' => 'Discontinued',
        ];
    }

    public static function fromCsvRecord(array $record): self
    {
        return new self([
            'code' => $record['Product Code'] ?? null,
            'name' => $record['Product Name'] ?? null,
            'description' => $record['Product Description'] ?? null,
            'stock' => $record['Stock'] ?? null,
            'cost' => $record['Cost in GBP'] ?? null,
            'discontinued' => $record['Discontinued'] ?? null,
        ]);
    }

    public function isDiscontinued(): bool
    {
        return $this->discontinued === self::DISCONTINUED_YES;
    }
}