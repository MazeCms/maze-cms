<?php

namespace maze\validators;
use maze\helpers\Html;

/**
 * DateValidator verifies if the attribute represents a date, time or datetime in a proper format.
 */
class DateValidator extends Validator
{
    /**
     * @var string the date format that the value being validated should follow.
     * Please refer to <http://www.php.net/manual/en/datetime.createfromformat.php> on
     * supported formats.
     */
    public $format = 'Y-m-d';
    /**
     * @var string the name of the attribute to receive the parsing result.
     * When this property is not null and the validation is successful, the named attribute will
     * receive the parsing result.
     */
    public $timestampAttribute;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = 'LIB_FRAMEWORK_VALIDATOR_DATE';
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;
        $result = $this->validateValue($value);
        if (!empty($result)) {
            $this->addError($object, $attribute, $result[0], $result[1]);
        } elseif ($this->timestampAttribute !== null) {
           
            $object->{$this->timestampAttribute} = strtotime($value);
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if (is_array($value)) {
            return [$this->message, []];
        }
        $date = strtotime($value); 
      
        $invalid = $date === false || $value !== date($this->format, $date) ;

        return $invalid ? [$this->message, []] : null;
    }
}
