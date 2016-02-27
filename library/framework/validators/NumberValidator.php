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
 * NumberValidator validates that the attribute value is a number.
 *
 * The format of the number must match the regular expression specified in [[integerPattern]] or [[numberPattern]].
 * Optionally, you may configure the [[max]] and [[min]] properties to ensure the number
 * is within certain range.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class NumberValidator extends Validator
{
    /**
     * @var boolean whether the attribute value can only be an integer. Defaults to false.
     */
    public $integerOnly = false;
    /**
     * @var integer|float upper limit of the number. Defaults to null, meaning no upper limit.
     */
    public $max;
    /**
     * @var integer|float lower limit of the number. Defaults to null, meaning no lower limit.
     */
    public $min;
    /**
     * @var string user-defined error message used when the value is bigger than [[max]].
     */
    public $tooBig;
    /**
     * @var string user-defined error message used when the value is smaller than [[min]].
     */
    public $tooSmall;
    /**
     * @var string the regular expression for matching integers.
     */
    public $integerPattern = '/^\s*[+-]?\d+\s*$/';
    /**
     * @var string the regular expression for matching numbers. It defaults to a pattern
     * that matches floating numbers with optional exponential part (e.g. -1.23e-10).
     */
    public $numberPattern = '/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = $this->integerOnly ? 'LIB_FRAMEWORK_VALIDATOR_NUMBER_INT'
                : 'LIB_FRAMEWORK_VALIDATOR_NUMBER';
        }
        if ($this->min !== null && $this->tooSmall === null) {
            $this->tooSmall = 'LIB_FRAMEWORK_VALIDATOR_NUMBER_MIN';
        }
        if ($this->max !== null && $this->tooBig === null) {
            $this->tooBig = 'LIB_FRAMEWORK_VALIDATOR_NUMBER_MAX';
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;
        if (is_array($value)) {
            $this->addError($object, $attribute, $this->message);
            return;
        }
        $pattern = $this->integerOnly ? $this->integerPattern : $this->numberPattern;
        if (!preg_match($pattern, "$value")) {
            $this->addError($object, $attribute, $this->message);
        }
        if ($this->min !== null && $value < $this->min) {
            $this->addError($object, $attribute, $this->tooSmall, ['min' => $this->min]);
        }
        if ($this->max !== null && $value > $this->max) {
            $this->addError($object, $attribute, $this->tooBig, ['max' => $this->max]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if (is_array($value)) {
            return ['LIB_FRAMEWORK_VALIDATOR_INVALID', []];
        }
        $pattern = $this->integerOnly ? $this->integerPattern : $this->numberPattern;
        if (!preg_match($pattern, "$value")) {
            return [$this->message, []];
        } elseif ($this->min !== null && $value < $this->min) {
            return [$this->tooSmall, ['min' => $this->min]];
        } elseif ($this->max !== null && $value > $this->max) {
            return [$this->tooBig, ['max' => $this->max]];
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($object, $attribute)
    {
        $label = $object->getAttributeLabel($attribute);

        $options = [
            'pattern' => new JsExpression($this->integerOnly ? $this->integerPattern : $this->numberPattern),
            'message' => \Text::_($this->message, ['attribute' => $label]),
        ];

        if ($this->min !== null) {
            $options['min'] = is_string($this->min) ? (float)$this->min : $this->min;
            $options['tooSmall'] = \Text::_($this->tooSmall, ['attribute' => $label]);
        }
        if ($this->max !== null) {
            $options['max'] = is_string($this->max) ? (float)$this->max : $this->max;
            $options['tooBig'] = \Text::_($this->tooBig, ['attribute' => $label]);
        }
        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        return ['validate'=>'number', 'options'=>$options];
    }
}
