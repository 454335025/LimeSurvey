<?php


namespace LimeSurvey\Datavalueobjects\qanda;
use LimeSurvey\Datavalueobjects\qanda\QuestionAttribute as QuestionAttribute;

class QuestionAttributes
{
    private array $list;
    private QuestionAttribute $current = null;

    /**
     * QuestionAttributes constructor.
     * @param array $list
     */
    public function __construct(array $list = []) {
        $this->addArrayAsAttributes($list);
    }

    /**
     * @param array $attributes Attributes as array
     */
    private function addArrayAsAttributes(array $attributes) : void
    {
        foreach($attributes as $attribute) {
            $current = new QuestionAttribute();
            $this->list = $this->addAttributeToList($current);
        }
    }

    /**
     * @param QuestionAttribute $attribute
     */
    public function addAttribute(QuestionAttribute $attribute) {
        $this->list[$attribute->getName()] = $attribute;
    }

    /**
     * @param QuestionAttribute $attribute
     * @return array
     */
    private function addAttributeToList(QuestionAttribute $attribute) {
        $this->list[$attribute->getName()] = $attribute;
        return $this->list;
    }

    /**
     * @return $this
     */
    public function removeAttribute(): self {

    }

    /**
     * @param string $name
     * @return QuestionAttribute
     */
    public function getAttribute(string $name): QuestionAttribute
    {
        return $this->list[$name];
    }


}
