<?php

/***
 * From qanda helper
 *
 * The $ia string comes from the $_SESSION['survey_'.Yii::app()->getConfig('surveyID')]['insertarray'] variable which is built at the commencement of the survey.
 * See index.php, function "buildsurveysession()"
 * One $ia array zexists for every question in the survey. The $_SESSION['survey_'.Yii::app()->getConfig('surveyID')]['insertarray']
 * string is an array of $ia arrays.
 *
 * $ia[0] => question id
 * $ia[1] => fieldname
 * $ia[2] => title
 * $ia[3] => question text
 * $ia[4] => type --  text, radio, select, array, etc
 * $ia[5] => group id
 * $ia[6] => mandatory Y || S || N
 * $ia[7] => conditions exist for this question
 * $ia[8] => other questions have conditions which rely on this question (including array_filter and array_filter_exclude attributes)
 * $ia[9] => incremental question count (used by {QUESTION_NUMBER})
 */

namespace LimeSurvey\Datavalueobjects;
use LimeSurvey\Datavalueobjects\Conditions as Conditions;


class Details
{
    private int $id = 0;
    private string $fieldName = '';
    private string $title = '';
    private string $questionText = '';
    private string $type;
    private int $groupID = 0;
    private string $mandatory = '';
    private Conditions $conditions;
    private Conditions $otherConditions;
    private int $incrementalQuestionCount = 0;


    public function __construct(int $id = 0,
                                string $fieldName = '',
                                string $title = '',
                                string $questionText = '',
                                string $type = '',
                                int $groupID = 0,
                                string $mandatory = '',
                                int $incrementalQuestionCount = 0,
                                Conditions $conditions = null,
                                Conditions $otherConditions = null)
    {
        $this->id = $id;
        $this->fieldName = $fieldName;
        $this->title = $title;
        $this->questionText = $questionText;
        $this->type = $type;
        $this->groupID = $groupID;
        $this->mandatory = $mandatory;
        $this->incrementalQuestionCount = $incrementalQuestionCount;

        if ($conditions !== null) {
            $this->conditions = $conditions;
        }
        if ($otherConditions !== null) {
            $this->otherConditions = $otherConditions;
        }
    }

    /**
     * @param int $id
     */
    public function setQuestionID(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getQuestionID(): int
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setFieldName(string $name): void
    {
        $this->fieldName = $name;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $text
     */
    public function setQuestionText(string $text): void
    {
        $this->questionText = $text;
    }

    /**
     * @return string
     */
    public function getQuestionText(): string
    {
        return $this->questionText;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param int $id
     */
    public function setGroupID(int $id): void
    {
        $this->groupID = $id;
    }

    /**
     * @return int
     */
    public function getGroupID(): int
    {
        return $this->groupID;
    }

    /**
     * @param string $mandatory
     */
    public function setMandatory(string $mandatory): void
    {
        $this->mandatory = $mandatory;
    }

    /**
     * @return string
     */
    public function getMandatory(): string
    {
        return $this->mandatory;
    }

    /**
     * @param string $condition
     */
    public function setCondition(string $condition): void
    {
        $this->condtions()->addCondtion($condition);
    }

    /**
     * @param string $name
     * @return Condition
     */
    public function getCondition(string $name): Condition
    {
        return $this->condtions()->getCondtion($name);
    }

    /**
     * @param \LimeSurvey\Datavalueobjects\Conditions $conditions
     */
    public function setConditons(Conditions $conditions): void
    {
        $this->conditions = $conditions;
    }

    /**
     * @return \LimeSurvey\Datavalueobjects\Conditions
     */
    public function getConditons(): Conditions
    {
        return $this->conditions;
    }

    /**
     * @param \LimeSurvey\Datavalueobjects\Conditions $otherConditions
     */
    public function setOtherConditions(Conditions $otherConditions): void
    {
        $this->otherConditions = $otherConditions;
    }

    /**
     * @return \LimeSurvey\Datavalueobjects\Conditions
     */
    public function getOtherConditions(): Conditions
    {
        return $this->otherConditions;
    }

    /**
     * @param int $incremental
     */
    public function incrementalQuestionCount(int $incremental): void
    {
        $this->incrementalQuestionCount = $incremental;
    }

    /**
     * @return int
     */
    public function getIncrementalQuestionCount(): int
    {
        return $this->incrementalQuestionCount;
    }

    public function asArray() : array
    {

    }
}
