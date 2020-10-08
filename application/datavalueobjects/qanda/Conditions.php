<?php

/***
 * From Question Helper.
 *
 * * $conditions element structure
 * $condition[n][0] => qid = question id
 * $condition[n][1] => cqid = question id of the target question, or 0 for TokenAttr leftOperand
 * $condition[n][2] => field name of element [1] (Except for type M or P)
 * $condition[n][3] => value to be evaluated on answers labeled.
 * $condition[n][4] => type of question
 * $condition[n][5] => SGQ code of element [1] (sub-part of [2])
 * $condition[n][6] => method used to evaluate
 * $condition[n][7] => scenario *NEW BY R.L.J. van den Burg*
 */

namespace LimeSurvey\Datavalueobjects;
use LimeSurvey\Datavalueobjects\Condition as Condition;

class Conditions
{
    private array $list;
    private Condition $conditon;

    public function __construct()
    {
        $this->list = [];
    }

    /**
     * @param Condition $condition
     * @return void
     */
    public function add(Condition $condition) : void
    {
        $this->list[$condition->getName()] = $condition;
    }

    /**
     * @param string $name
     * @return Condition
     */
    public function getCondition(string $name): Condition
    {
        return $this->list[$name];
    }

    /**
     * @return array
     */
    public function getAllConditions(): array
    {
        return $this->list;
    }
}
