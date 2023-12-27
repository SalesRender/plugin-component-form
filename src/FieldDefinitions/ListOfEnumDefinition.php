<?php
/**
 * Created for plugin-component-form
 * Datetime: 29.08.2019 12:40
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;


use SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Limit;
use SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Values\ValuesListInterface;

class ListOfEnumDefinition extends FieldDefinition
{

    private ValuesListInterface $values;

    private ?Limit $limit;

    public function __construct(
        string $title,
        ?string $description,
        callable $validator,
        ValuesListInterface $valuesList,
        ?Limit $limit,
        $default = null,
        $context = null
    )
    {
        parent::__construct($title, $description, $validator, $default, $context);
        $this->values = $valuesList;
        $this->limit = $limit;
    }

    public function getLimit(): ?Limit
    {
        return $this->limit;
    }

    public function getValues(): ValuesListInterface
    {
        return $this->values;
    }


    public function getDefinition(): string
    {
        return 'listOfEnum';
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'limit' => $this->getLimit(),
            'values' => $this->getValues()
        ]);
    }

}