<?php

class TestCase extends PHPUnit_Framework_TestCase
{

    protected function assignAndGetErrors(\yii\base\Model $form, $field, $value)
    {
        $form->$field = $value;
        $form->validate($field);

        return $form->hasErrors($field);
    }

    public function assertValidateTrue($form, $field, $value, $message = "")
    {
        $this->assertFalse($this->assignAndGetErrors($form, $field, $value), $message);
    }

    public function assertValidateFalse($form, $field, $value, $message = "")
    {
        $this->assertTrue($this->assignAndGetErrors($form, $field, $value), $message);
    }

    public function getAssertValidate($form, $field)
    {
        return function ($value, $message, $assert) use ($form, $field) {
            if ($assert) {
                $this->assertValidateTrue($form, $field, $value, $message);
            } else {
                $this->assertValidateFalse($form, $field, $value, $message);
            }
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
