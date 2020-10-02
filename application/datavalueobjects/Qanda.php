<?php


namespace LimeSurvey\Datavalueobjects;
use LimeSurvey\Datavalueobjects\Details as Details;


class Qanda
{
    private const CHECKED = 'checked = "checked"';
    private const SELECTED = 'selected = "selected"';
    private const SHOW_NO_ANSWER = 0;

    private Details $details;
    private array $fieldnames = [];

    /**
     * Qanda constructor.
     * @param \LimeSurvey\Datavalueobjects\Details $details
     */
    public function __construct(Details $details)
    {
        $this->details = $details;
    }

    /**
     * Sets NoAnswerCode.
     *
     * @param $thissurvey ??
     */
    public function setNoAnswerMode($thissurvey): void
    {
        $shownoanswerString = 'shownoanswer';
        $config = App()->getConfig($shownoanswerString);
        if ($config === 2) {
            if ($thissurvey[$shownoanswerString] === 'N') {
                $this->SHOW_NO_ANSWER = 0;
            } else {
                $this->SHOW_NO_ANSWER = 1;
            }
        } else if ($config === 1) {
            $this->SHOW_NO_ANSWER = 1;
        } else if ($config === 0) {
            $this->SHOW_NO_ANSWER = 0;
        } else {
            $this->SHOW_NO_ANSWER = 1;
        }
    }

    /**
     * This function returns an array containing the 'question/answer' html display
     * and a list of the question/answer fieldnames associated.
     * It is called from question.php, group.php, survey.php and preview.php.
     *
     * @param \LimeSurvey\Datavalueobjects\Details $details
     */
    public function retrieveAnswers(Details $details): array
    {
        $result = [];

        //globalise required config variables
        global $thissurvey; //These are set by index.php // TODO: Create an object for this.

        // TODO: This can be cached in some special cases.
        // 1. If move back is disabled
        // 2. No tokens
        // 3. Always first time it's shown to one user (and no tokens).
        // 4. No expressions with tokens or time or other dynamic features.
        $surveyID   = $_SESSION['survey_'] . $thissurvey['sid'];
        $cacheQanda = EmCacheHelper::cacheQanda($details, $surveyID);
        if ($cacheQanda) {
            $cacheKey = 'retrieveAnswers_' . sha1(implode('_', $details->asArray()));   // TODO: Implement as Array() Method.
            $value = EmCacheHelper::get($cacheKey);
            if (!$value) {
                $result = $value;
            }
        }

        $display = $details->getConditons(); // Display
        $questionID = $details->getQuestionID(); // Question ID
        $questionTitle = $details->getQuestionText(); // Question Text

        $inputNames = [];
        $answer = ""; // Create the question/answer html

        $incrementalQuestionCount = $details->getIncrementalQuestionCount();

        if (isset($incrementalQuestionCount)) {
            $number = $incrementalQuestionCount; // Previously in limesurvey, it was virtually impossible to control how the start of questions were formatted. // this is an attempt to allow users (or rather system admins) some control over how the starting text is formatted.
        } else {
            $number = 0; // TODO: In Code it was ''
        }

    }
}
