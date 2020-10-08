<?php


namespace LimeSurvey\Datavalueobjects;
use EmCacheHelper;
use LimeSurvey\Datavalueobjects\Details as Details;
use LimeSurvey\Datavalueobjects\qanda\QuestionAttributes;
use Question;
use QuestionAttribute;
use Survey;


class Qanda
{
    private const CHECKED = 'checked = "checked"';
    private const SELECTED = 'selected = "selected"';
    private const SHOW_NO_ANSWER = 0;

    private Details $details;
    private array $fieldnames = [];

    /**
     * Qanda constructor.
     * @param Details $details
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
     * @param Details $details
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
        $cacheQanda = EmCacheHelper::cacheQanda($details->asArray(), $surveyID);
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

    /**
     * @param int $surveyID
     * @param string $baseName
     * @param string $name
     * @param QuestionAttribute $questionAttribute
     * @return string
     */
    public function getCurrentRelevanceClass(int $surveyID, string $baseName, string $name, QuestionAttribute $questionAttribute) : string
    {
        $result = '';

        $relevanceStatus = !isset($_SESSION["survey_{$surveyID}"]['relevanceStatus'][$name]) || $_SESSION["survey_{$surveyID}"]['relevanceStatus'][$name];
        if (!$relevanceStatus) {
            $excludeAllOther = $questionAttribute->getExcludeAllOther();
            $isExluded = isset($excludeAllOther);

            /* EM don't set difference between relevance in session, if exclude_all_others is set , just ls-disabled */
            if ($isExluded) {
                $excludes = explode(';', $excludeAllOther);
                foreach ($excludes as $exclude) {
                    $exclude = $baseName . $exclude;

                    $sessionIsRelevanceStatus = (!isset($_SESSION["survey_{$surveyId}"]['relevanceStatus'][$sExclude]) || $_SESSION["survey_{$surveyId}"]['relevanceStatus'][$sExclude]);
                    $sessionIsExcluded = isset($_SESSION["survey_{$surveyId}"][$sExclude]) && $_SESSION["survey_{$surveyId}"][$sExclude] == "Y");
                    if ($sessionIsRelevanceStatus && $sessionIsExcluded) {
                        $result = 'ls-irrelevant ls-disabled';
                    }
                }
            }

            $arrayFilterStyle = $questionAttribute->getArrayFilterStyle();
            $filterStyleIsNotEmpty = !empty($arrayFilterStyle);
            if ($filterStyleIsNotEmpty) {
                $result = 'ls-irrelevant ls-disabled';
            }
        }

        $result = 'ls-irrelevant ls-hidden';
        return $result;
    }

    /**
     * @param Details $details
     * @param QuestionAttributes $attributes
     * @param Survey $survey
     * @param string $rowName
     * @param string $method
     * @param null $class
     * @return array
     */
    public function returnArrayFilterStrings(Details $details, QuestionAttributes $attributes, Survey $survey, string $rowName, $method = 'tbody', $class = null) : array
    {
        $htmlBodyStringTwo = "\n\n\t<$method id='javatbd$rowName'";
        if ($class !== null) {
            $htmlBodyStringTwo .= " class='$class'";
        } else {
            $htmlBodyStringTwo .= "";
        }

        $surveyid = $survey->id;

        $sessionRelevanceStatus = $_SESSION["survey_{$surveyid}"]['relevanceStatus'][$rowName];
        $isIssetSessionRelevanceStatus = isset($sessionRelevanceStatus);
        $isNotSessionRelevanceStatus = !$sessionRelevanceStatus;

        $attributeArrayFilterStyle = $attributes->getAttribute('array_filter_style');

        if ($isIssetSessionRelevanceStatus && $isNotSessionRelevanceStatus) {
            // If using exclude_all_others, then need to know whether irrelevant rows should be hidden or disabled
            $excludeAllOthers = $attributes->getAttribute('exclude_all_others');
            if ($excludeAllOthers !== null) {
                $disableIt = false;
                foreach ($excludeAllOthers as $excludeAll) {
                    $row = $details->getFieldName().$excludeAll;

                    $sessionRelevanceStatusRow = $_SESSION["survey_{$surveyid}"]['relevanceStatus'][$row];
                    $isIssetSessionRelevanceStatusRow = isset($sessionRelevanceStatusRow);
                    $sessionRow = $_SESSION[$row];
                    $isIssetSessionRow = isset($sessionRow);
                    $sessionRowIsYes = $sessionRow === 'Y';

                    if (($isIssetSessionRelevanceStatusRow || $sessionRelevanceStatusRow) && $isIssetSessionRow && $sessionRowIsYes) {
                        $disableIt = true;
                    }
                }

                $attributeArrayFilterStyle = $attributes->getAttribute('array_filter_style');

                if ($disableIt) {
                    $htmlBodyStringTwo .= " disabled='disabled'";
                } else if (!isset($attributeArrayFilterStyle) || $attributeArrayFilterStyle === '0') {
                    $htmlBodyStringTwo .= " style='display: none'";
                } else {
                    $htmlBodyStringTwo .= " disabled='disabled'";
                }
            } else if (!isset($attributeArrayFilterStyle) || $attributeArrayFilterStyle === '0') {
                $htmlBodyStringTwo .= " style='display: none'";
            } else {
                $htmlBodyStringTwo .= " disabled='disabled'";
            }
        }
        $htmlBodyStringTwo .= ">\n";
        return array($htmlBodyStringTwo, "");
    }

    /**
     * @param string $useKeyPad
     * @return string
     */
    public function testKeyPad(string $useKeyPad): string
    {
        $class = '';
        if ($useKeyPad === 'Y') {
            self.includeKeyPad();
            $class = 'text-keypad';
        }
        return $class;
    }

    /**
     * Includes Keypad headers.
     */
    private function includeKeyPad()
    {
        $thirdPartyFolderName = 'third_party';
        $rootDirFolderName = 'rootdir';
        $jQueryKeyPadFolder = 'jquery-keypad';
        $jQueryKeyPadFileName = 'jquery.keypad-';
        $jQueryCSSKeyPadFilename = 'jquery.keypad.alt.css';
        $jsFileEnding = '.js';

        App()->getClientScript()->registerScriptFile(App()->getConfig($thirdPartyFolderName).$jQueryKeyPadFolder.'/jquery.plugin.min.js');
        App()->getClientScript()->registerScriptFile(App()->getConfig($thirdPartyFolderName).$jQueryKeyPadFolder.'/jquery.keypad.min.js');

        $language = App()->language;
        $file = App()->getConfig($rootDirFolderName).'/'.$thirdPartyFolderName.'/'.$jQueryKeyPadFolder.'/'.$jQueryKeyPadFileName.$language.$jsFileEnding;
        if ($language !== 'en' && file_exists($file)) {
            App()->getClientScript()->registerScriptFile(App()->getConfig($thirdPartyFolderName).$jQueryKeyPadFolder.'/'.$jQueryKeyPadFileName.$language.$jsFileEnding);
        }
        App()->getClientScript()->registerCssFile(App()->getConfig($thirdPartyFolderName).$jQueryKeyPadFolder.$jQueryKeyPadFolder.'/'.$jQueryCSSKeyPadFilename);
    }

    /**
     * @param Details $details
     * @return array
     */
    public function showLanguageQuestions(Details $details) : array
    {
        $id = App()->getConfig('surveyID');
        $survey = Survey::model()->findByPk($id);
        $checkConditonsFunction = 'checkconditions';
        $answerLanguages = $survey->additionalLanguages;
        $answerLanguages[] = $survey->language;

        $language = $_SESSION['survey_' . $id]['s_lang'];
        $coreClass = "ls-answers answer-item dropdow-item langage-item";
        $inputNames = [];

        // TODO: Change this part to private method.
        if (!in_array($language, $answerLanguages)) {
            $language = $survey->language;
        }

        $inputNames = $details->getFieldName();

        $languageData = [
            'name' => $details->getFieldName(),
            'basename' => $details->getFieldName(),
            'checkconditionFunction' => $checkconditionFunction.'(this.value, this.name, this.type)',
            'answerlangs' => $answerLanguages,
            'sLangs' => $language,
            'coreClass' => $coreClass,
        ];

        $answer = $this->service->doRender('/survey/questions/answer/language/answer', $languageData, true);
        return array($answer, $inputNames);
    }

    /**
     * @todo Can remove Db
     * @param Details $details
     * @return array
     */
    public function showListDropDownQuestion(Details $details) : array
    {
        $inputNames = [];
        $checkconditionFunction = 'checkconditions';

        // Question Attribute variables
        $questionAttributes = QuestionAttribute::model()->getQuestionAttributes($details->getQuestionID());
        $attributes = new QuestionAttributes($questionAttributes);
        $surveyID = App()->getConfig('surveyID');
        $language = $_SESSION['survey_'. $surveyID]['s_lang'];
        $otherText = trim($attributes->getAttribute('other_replace_text')->inLanguage($language));
        $categorySeperator = trim($attributes->getAttribute('category_separator'));

        if ($otherText === '') {
            $otherText = gT('Other:'); // text for 'other'
        }

        if ($categorySeperator === '') {
            unset($categorySeperator);
        }

        $coreClass = "ls-answers answer-item dropdown-item";
        $question  = Question::model()->findByPk(array('qid' => $details->getQuestionID(), 'language' => $language));
        $other = $question->other;

        $answer = $question->getOrderedAnswers($attributes->getAttribute('random_order'), $attributes->getAttribute('alphasort'));
        $dropDownSize = $attributes->getAttribute('dropdown_size');
        $dropDownSizeIsIsset = isset($dropDownSize);
        if ($dropDownSizeIsIsset && $dropDownSize > 0) {
           $height = sanitize_int($dropDownSize);
           $maxHeight = count($answer);

           $sessionFieldName = $_SESSION['survey_'.App()->getConfig('surveyID')][$details->getFieldName()];
           $sessionFieldNameIsNotNull = !is_null($sessionFieldName);
           $sessionFiledNameIsNotEmpty = ($sessionFieldName === '');
           $mandatory = $details->getMandatory();

           if (($sessionFieldNameIsNotNull || $sessionFiledNameIsNotEmpty) && $mandatory !== 'Y' && $mandatory !== 'S' && $this->SHOW_NO_ANSWER == 1) {
                ++$maxHeight; // for No Answer
           }

           if (isset($other) && $other === 'Y') {
               ++$maxHeight; // for Other
           }

           if (is_null())
        }


    }
}
