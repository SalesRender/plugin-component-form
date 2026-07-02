<?php
/**
 * Created for plugin-form.
 * Datetime: 02.07.2018 15:33
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;


use JsonSerializable;
use SalesRender\Plugin\Components\Form\Components\ValidatorInterface;
use SalesRender\Plugin\Components\Form\FormData;

abstract class FieldDefinition implements JsonSerializable
{

    public string $title;

    public ?string $description;

    /** @var callable */
    protected $validator;

    public mixed $default;

    public mixed $context;

    /**
     * ConfigDefinition constructor.
     * @param string $title
     * @param string|null $description
     * @param ValidatorInterface|callable $validator
     * @param null $default
     * @param null $context
     */
    public function __construct(string $title, ?string $description, ValidatorInterface|callable $validator, mixed $default = null, mixed $context = null)
    {
        $this->title = $title;
        $this->description = $description;

        $this->validator = $validator;
        $this->default = $default;
        $this->context = $context;
    }

    public function validate($value, FormData $data): bool
    {
        return empty($this->getErrors($value, $data));
    }

    public function getErrors($value, FormData $data): array
    {
        return ($this->validator)($value, $this, $data);
    }

    abstract public function getDefinition(): string;

    public function jsonSerialize(): mixed
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'definition' => $this->getDefinition(),
            'default' => $this->default,
            'context' => $this->context,
        ];
    }

}