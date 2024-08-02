<?php
/**
 * Created for plugin-form
 * Datetime: 04.07.2019 16:18
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Form;


use JsonSerializable;
use SalesRender\Plugin\Components\Form\Exceptions\InvalidDependencyException;
use SalesRender\Plugin\Components\Form\FieldDefinitions\FieldDefinition;
use SalesRender\Plugin\Components\Form\MarkdownPreview\MarkdownPreviewInterface;
use SalesRender\Plugin\Components\Form\TableView\TablePreviewInterface;
use TypeError;

class FieldGroup implements JsonSerializable
{

    protected string $title;

    protected ?string $description;

    /** @var FieldDefinition[] */
    protected array $fields = [];

    /** @var array[] */
    protected array $dependencies = [];

    private array $context = [];

    /**
     * FieldsGroup constructor.
     * @param string $title
     * @param string|null $description
     * @param FieldDefinition[]|TablePreviewInterface[]|MarkdownPreviewInterface[] $fields
     * @param array $dependencies
     * @param array $context
     * @throws InvalidDependencyException
     */
    public function __construct(string $title, ?string $description, array $fields, array $dependencies = [], array $context = [])
    {
        $this->title = $title;
        $this->description = $description;

        foreach ($fields as $name => $fieldsGroup) {
            if (!$fieldsGroup instanceof FieldDefinition) {
                throw new TypeError('Every item of $fields should be instance of ' . FieldDefinition::class);
            }
            $this->fields[$name] = $fieldsGroup;
        }

        foreach ($dependencies as $field => $dependsFrom) {
            if (!isset($this->fields[$field])) {
                throw new InvalidDependencyException('Invalid field name "' . $field . '" in dependency key', 100);
            }

            foreach ($dependsFrom as $depFromField) {
                if (!isset($this->fields[$depFromField])) {
                    throw new InvalidDependencyException('Invalid dependency "' . $depFromField . '" for "' . $field . '"', 200);
                }
            }
        }

        $this->dependencies = $dependencies;
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

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'fields' => $this->getFields(),
            'dependencies' => $this->getDependencies(),
        ];
    }
}