<?php
/**
 * Created for plugin-component-form
 * Date: 05.10.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\Autocomplete;


interface AutocompleteInterface
{

    public function query(string $query): array;

    public function values(array $values): array;

    public function validate(array $values): bool;

}