<?php

class TestCase extends PHPUnit_Framework_TestCase
{
    public function getAssertValidate($form, $field)
    {
        return function ($value, $message, $assert) use ($form, $field) {
            $form->$field = $value;
            $this->assertEquals($assert, $form->validate([$field]), $message);
        };
    }

    /**
     * Usage:
     *
     * ```php
     * $specifyField = $this->getAssertValidate(new SpecifyForm(), 'specify_field');
     *
     * $expect = [
     *   ['', 'specify_field can not be empty', false],
     * ];
     *
     * $this->expectValidate($expect, $specifyField);
     *
     * ```
     *
     * @param array $expect
     * @param Closure $func
     */
    public function expectValidate(array $expect, Closure $func)
    {
        foreach ($expect as $row) {
            $func($row[0], $row[1], $row[2]);
        }
    }
}
