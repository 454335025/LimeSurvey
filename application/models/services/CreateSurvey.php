<?php


namespace LimeSurvey\Models\Services;

use Survey;
use SurveyLanguageSetting;
use Permission;

/**
 * This class is responsible for creating a new survey.
 *
 * Class CreateSurvey
 * @package LimeSurvey\Models\Services
 */
class CreateSurvey
{
    /** @var int number of attempts tp find a valid survey id */
    const ATTEMPTS_CREATE_SURVEY_ID = 50;

    /** @var string all attributes that have the value "NO" */
    const STRING_VALUE_FOR_NO_FALSE = 'N';

    /** @var string all attributes that have the value "YES" */
    const STRING_VALUE_FOR_YES_TRUE = 'Y';

    /** @var string value to set attribute to inherit */
    const STRING_SHORT_VALUE_INHERIT = 'I';

    /** @var int */
    const INTEGER_VALUE_FOR_INHERIT = -1;

    /** @var Survey the survey */
    private $survey;

    /** @var \LimeSurvey\Models\Services\SimpleSurveyValues has the simple values for creating a survey */
    private $simpleSurveyValues;

    /**
     * CreateSurvey constructor.
     *
     * @param Survey $survey the survey object
     */
    public function __construct($survey)
    {
        $this->survey = $survey;
    }

    /**
     * This creates a simple survey with the basic attributes set in constructor.
     *
     * @param SimpleSurveyValues $simpleSurveyValues
     *
     * @return Survey|bool returns the survey or false if survey could not be created for any reason
     */
    public function createSimple($simpleSurveyValues){

        $this->simpleSurveyValues = $simpleSurveyValues;
        $this->survey->gsid = $simpleSurveyValues->getSurveyGroupId();
        try {
            $this->createSurveyId();
            $this->setBaseLanguage();
            $this->initialiseSurveyAttributes();

            if(!$this->survey->save()){
                throw new \Exception("Survey value/values are not valid. Not possible to save survey");
            }

            //check realtional tables to be initialised like survey_languagesettings
            $this->createRelationSurveyLanguageSettings();

            // Update survey permissions
            Permission::model()->giveAllSurveyPermissions(\Yii::app()->session['loginID'], $this->survey->sid);

        }catch (\Exception $e){
            return false;
        }

        return $this->survey;
    }

    /**
     *
     *
     * @return void
     * @throws \Exception
     */
    private function createRelationSurveyLanguageSettings(){
        $sTitle = html_entity_decode($this->simpleSurveyValues->getTitle(), ENT_QUOTES, "UTF-8");

        // Fix bug with FCKEditor saving strange BR types
        $sTitle = fixCKeditorText($sTitle);
        $dateFormat = 1; //default value
        if($dateFormat === null || ($dateFormat<1) || ($dateFormat>12) ){
            //dateformat is not past correctly from frontend to backend
            $dateFormat = 1;
        }

        // Insert base language into surveys_language_settings table
        $aInsertData = array(
            'surveyls_survey_id' => $this->survey->sid,
            'surveyls_title' => $sTitle,
            'surveyls_description' => '',
            'surveyls_welcometext' => '',
            'surveyls_language' => $this->simpleSurveyValues->getBaseLanguage(),
            'surveyls_urldescription' => '',
            'surveyls_endtext' => '',
            'surveyls_url' => '',
            'surveyls_dateformat' => $dateFormat,
            'surveyls_numberformat' => 0, //todo is this the correct default value?
            'surveyls_policy_notice' => '',
            'surveyls_policy_notice_label' => ''
        );

        $langsettings = new SurveyLanguageSetting;
        if(!$langsettings->insertNewSurvey($aInsertData)){
            throw new \Exception('SurveyLanguageSettings could not be created');
        }
    }

    /**
     * Sets the baselanguage. If baselanguag is null or empty string Exception is thrown.
     *
     * @throws \Exception  if $this->baseLanguage is null or empty string
     */
    private function setBaseLanguage(){
        $baseLang = $this->simpleSurveyValues->getBaseLanguage();
        if($baseLang !== null && $baseLang!==''){
            $this->survey->language = $baseLang;

            //todo: check the shortname of language (e.g. 'en')
        }else{
            throw new \Exception("Invalid language");
        }
    }

    /**
     * Creates a unique survey id. A survey id always consists of 6 numbers [123456789].
     *
     * If not possible within ATTEMPTS_CREATE_SURVEY_ID an Exception is thrown
     *
     * @throws \Exception
     */
    private function createSurveyId(){
        $attempts = 0;
        /* Validate sid : > 1 and unique */
        $this->survey->sid = intval(randomChars(6, '123456789'));
        while(!$this->survey->validate(array('sid'))) {
            $attempts++;
            $this->survey->sid = intval(randomChars(6, '123456789'));
            /* If it's happen : there are an issue in server … (or in randomChars function …) */
            if($attempts > self::ATTEMPTS_CREATE_SURVEY_ID) {
                throw new \Exception("Unable to get a valid survey id after ". self::ATTEMPTS_CREATE_SURVEY_ID . " attempts");
            }
        }
    }

    /**
     *
     */
    private function initialiseSurveyAttributes(){

        $this->survey->expires = null;
        $this->survey->startdate = null;
        $this->survey->template = 'inherit'; //default template from default group is set to 'fruity'
        $this->survey->admin = 'inherit'; //admin name ...
        $this->survey->active = self::STRING_VALUE_FOR_NO_FALSE;
        $this->survey->anonymized = self::STRING_VALUE_FOR_NO_FALSE;
        $this->survey->faxto = null;
        $this->survey->format = self::STRING_SHORT_VALUE_INHERIT; //inherits value from survey group
        $this->survey->savetimings = self::STRING_SHORT_VALUE_INHERIT; //could also be 'I' for inherit from survey group ...
        $this->survey->language = $this->simpleSurveyValues->getBaseLanguage();
        $this->survey->datestamp = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->ipaddr = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->ipanonymize = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->refurl = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->usecookie = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->emailnotificationto = 'inherit';
        $this->survey->allowregister = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->allowsave = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->navigationdelay = self::INTEGER_VALUE_FOR_INHERIT;
        $this->survey->autoredirect = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->showxquestions = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->showgroupinfo = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->showqnumcode = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->shownoanswer = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->showwelcome = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->allowprev =  self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->questionindex = self::INTEGER_VALUE_FOR_INHERIT;
        $this->survey->nokeyboard = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->showprogress = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->printanswers = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->listpublic = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->htmlemail = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->sendconfirmation = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->tokenanswerspersistence = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->alloweditaftercompletion = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->usecaptcha = 'E'; // see Survey::saveTranscribeCaptchaOptions() special inherit char ...
        $this->survey->publicstatistics = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->publicgraphs = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->assessments = self::STRING_SHORT_VALUE_INHERIT;
        $this->survey->emailresponseto = 'inherit';
        $this->survey->tokenlength = self::INTEGER_VALUE_FOR_INHERIT;
        $this->survey->adminemail = 'inherit';
        $this->survey->bounce_email = 'inherit';
    }

}
