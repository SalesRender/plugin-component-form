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

    protected string $title;

    protected ?string $description;

    /** @var callable */
    protected $validator;

    /** @var mixed|null */
    protected $default;

    /** @var mixed|null */
    protected $context;

    /**
     * ConfigDefinition constructor.
     * @param string $title
     * @param string|null $description
     * @param ValidatorInterface|callable $validator
     * @param null $default
     * @param null $context
     */
    public function __construct(string $title, ?string $description, callable $validator, $default = null, $context = null)
    {
        $this->title = $title;
        $this->description = $description;

        $this->validator = $validator;
        $this->default = $default;
        $this->context = $context;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function validate($value, FormData $data): bool
    {
        return empty($this->getErrors($value, $data));
    }

    public function getErrors($value, FormData $data): array
    {
        return ($this->validator)($value, $this, $data);
    }

    /**
     * @return mixed|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return mixed|null
     */
    public function getContext()
    {
        return $this->context;
    }

    abstract public function getDefinition(): string;

    public function jsonSerialize()
    {
        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'definition' => $this->getDefinition(),
            'default' => $this->getDefault(),
            'context' => $this->getContext(),
        ];
    }

}