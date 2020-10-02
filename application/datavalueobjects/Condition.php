<?php


namespace LimeSurvey\Datavalueobjects;


class Condition
{
    private string $name;
    private string $questionID;
    private string $fieldName;
    private string $evaluatedQuestion;
    private string $tokenAttribute;
    private string $type;

    public function __construct()
    {

    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
