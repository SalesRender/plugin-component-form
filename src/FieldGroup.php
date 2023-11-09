<?php
/**
 * Created for plugin-form
 * Datetime: 04.07.2019 16:18
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Form;


use JsonSerializable;
use SalesRender\Plugin\Components\Form\FieldDefinitions\FieldDefinition;
use TypeError;

class FieldGroup implements JsonSerializable
{

    protected string $title;

    protected ?string $description;

    /** @var FieldDefinition[] */
    protected array $fields = [];

    /**
     * FieldsGroup constructor.
     * @param string $title
     * @param string|null $description
     * @param FieldDefinition[] $fields
     */
    public function __construct(string $title, ?string $description, array $fields)
    {
        $this->title = $title;
        $this->description = $description;

        foreach ($fields as $name => $fieldsGroup) {
            if (!$fieldsGroup instanceof FieldDefinition) {
                throw new TypeError('Every item of $fields should be instance of ' . FieldDefinition::class);
            }
            $this->fields[$name] = $fieldsGroup;
        }
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function jsonSerialize()
    {
        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'fields' => $this->getFields(),
        ];
    }
}