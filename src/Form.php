<?php
/**
 * Created for plugin-form
 * Datetime: 02.07.2018 16:59
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Form;


use JsonSerializable;
use TypeError;

class Form implements JsonSerializable
{

    protected string $title;

    protected ?string $description;

    /** @var FieldGroup[] */
    protected array $groups = [];

    private string $button;

    private array $context = [];

    /**
     * Form constructor.
     * @param string $title
     * @param string|null $description
     * @param FieldGroup[] $fieldGroups
     * @param string $button
     * @param array $context
     */
    public function __construct(string $title, ?string $description, array $fieldGroups, string $button, array $context = [])
    {
        $this->title = $title;
        $this->description = $description;

        foreach ($fieldGroups as $groupName => $fieldsGroup) {
            if (!$fieldsGroup instanceof FieldGroup) {
                throw new TypeError('Every item of $fieldsDefinitions should be instance of ' . FieldGroup::class);
            }
            $this->groups[$groupName] = $fieldsGroup;
        }

        $this->button = $button;
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

    /**
     * @return FieldGroup[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    public function getButton(): string
    {
        return $this->button;
    }

    public function getDefaultData(): FormData
    {
        $data = [];
        foreach ($this->getGroups() as $groupName => $group) {
            foreach ($group->getFields() as $fieldName => $field) {
                $data[$groupName][$fieldName] = $field->getDefault();
            }
        }
        return new FormData($data);
    }

    public function clearRedundant(FormData $formData): FormData
    {
        $dataGroupPath = [];
        $dataFieldsPath = [];
        foreach ($formData as $groupName => $fields) {
            $dataGroupPath[$groupName] = $groupName;
            foreach ($fields as $fieldName => $value) {
                $dataFieldsPath[] = "{$groupName}.{$fieldName}";
            }
        }

        $formGroupPath = [];
        $formFieldsPath = [];
        foreach ($this->getGroups() as $groupName => $group) {
            $formGroupPath[$groupName] = $groupName;
            foreach ($group->getFields() as $fieldName => $field) {
                $formFieldsPath[] = "{$groupName}.{$fieldName}";
            }
        }

        $result = clone $formData;
        $result->delete(array_diff($dataFieldsPath, $formFieldsPath));
        $result->delete(array_diff($dataGroupPath, $formGroupPath));
        return $result;
    }

    public function validateData(FormData $formData): bool
    {
        foreach ($this->getGroups() as $groupName => $group) {
            foreach ($group->getFields() as $fieldName => $field) {
                $path = "{$groupName}.{$fieldName}";
                $value = $formData->get($path);
                if (!$field->validate($value, $formData)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getErrors(FormData $formData): array
    {
        $errors = [];
        foreach ($this->getGroups() as $groupName => $group) {
            foreach ($group->getFields() as $fieldName => $field) {
                $path = "{$groupName}.{$fieldName}";
                $value = $formData->get($path);
                $error = $field->getErrors($value, $formData);
                if (!empty($error)) {
                    $errors[$groupName][$fieldName] = $error;
                }
            }
        }
        return $errors;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    public function jsonSerialize()
    {
        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'groups' => $this->getGroups(),
            'button' => $this->getButton(),
        ];
    }
}