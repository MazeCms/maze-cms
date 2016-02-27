<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace maze\validators;
use maze\helpers\Html;
use maze\base\JsExpression;
/**
 * StringValidator validates that the attribute value is of certain length.
 *
 * Note, this validator should only be used with string-typed attributes.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class StringValidator extends Validator
{
    /**
     * @var integer|array specifies the length limit of the value to be validated.
     * This can be specified in one of the following forms:
     *
     * - an integer: the exact length that the value should be of;
     * - an array of one element: the minimum length that the value should be of. For example, `[8]`.
     *   This will overwrite [[min]].
     * - an array of two elements: the minimum and maximum lengths that the value should be of.
     *   For example, `[8, 128]`. This will overwrite both [[min]] and [[max]].
     */
    public $length;
    /**
     * @var integer maximum length. If not set, it means no maximum length limit.
     */
    public $max;
    /**
     * @var integer minimum length. If not set, it means no minimum length limit.
     */
    public $min;
    /**
     * @var string user-defined error message used when the value is not a string
     */
    public $message;
    /**
     * @var string user-defined error message used when the length of the value is smaller than [[min]].
     */
    public $tooShort;
    /**
     * @var string user-defined error message used when the length of the value is greater than [[max]].
     */
    public $tooLong;
    /**
     * @var string user-defined error message used when the length of the value is not equal to [[length]].
     */
    public $notEqual;
    /**
     * @var string the encoding of the string value to be validated (e.g. 'UTF-8').
     * If this property is not set, [[\yii\base\Application::charset]] will be used.
     */
    public $encoding;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (is_array($this->length)) {
            if (isset($this->length[0])) {
                $this->min = $this->length[0];
            }
            if (isset($this->length[1])) {
                $this->max = $this->length[1];
            }
            $this->length = null;
        }
        if ($this->encoding === null) {
            $this->encoding = 'utf-8';
        }
        if ($this->message === null) {
            $this->message = 'LIB_FRAMEWORK_VALIDATOR_STRINGNOT';
        }
        if ($this->min !== null && $this->tooShort === null) {
            $this->tooShort = 'LIB_FRAMEWORK_VALIDATOR_STRING_MIN';
        }
        if ($this->max !== null && $this->tooLong === null) {
            $this->tooLong = 'LIB_FRAMEWORK_VALIDATOR_STRING_MAX';
        }
        if ($this->length !== null && $this->notEqual === null) {
            $this->notEqual = 'LIB_FRAMEWORK_VALIDATOR_STRING_LENGTH';
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;

        if (!is_string($value)) {
            $this->addError($object, $attribute, $this->message);

            return;
        }

        $length = mb_strlen($value, $this->encoding);

        if ($this->min !== null && $length < $this->min) {
            $this->addError($object, $attribute, $this->tooShort, ['min' => $this->min]);
        }
        if ($this->max !== null && $length > $this->max) {
            $this->addError($object, $attribute, $this->tooLong, ['max' => $this->max]);
        }
        if ($this->length !== null && $length !== $this->length) {
            $this->addError($object, $attribute, $this->notEqual, ['length' => $this->length]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if (!is_string($value)) {
            return [$this->message, []];
        }

        $length = mb_strlen($value, $this->encoding);

        if ($this->min !== null && $length < $this->min) {
            return [$this->tooShort, ['min' => $this->min]];
        }
        if ($this->max !== null && $length > $this->max) {
            return [$this->tooLong, ['max' => $this->max]];
        }
        if ($this->length !== null && $length !== $this->length) {
            return [$this->notEqual, ['length' => $this->length]];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($object, $attribute)
    {
        $label = $object->getAttributeLabel($attribute);

        $options = [
            'message' => \Text::_($this->message, ['attribute' => $label]),
        ];

        if ($this->min !== null) {
            $options['min'] = $this->min;
            $options['tooShort'] = \Text::_($this->tooShort, ['attribute' => $label,'min' => $this->min]);
        }
        if ($this->max !== null) {
            $options['max'] = $this->max;
            $options['tooLong'] = \Text::_($this->tooLong, ['attribute' => $label,'max' => $this->max]);
        }
        if ($this->length !== null) {
            $options['length'] = $this->length;
            $options['notEqual'] = \Text::_($this->notEqual, ['attribute' => $label,'length' => $this->length]);
        }
        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        return ['validate'=>'string', 'options'=>$options];
    }
}
