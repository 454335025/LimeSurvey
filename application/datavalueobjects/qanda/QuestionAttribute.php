<?php


namespace LimeSurvey\Datavalueobjects\qanda;


class QuestionAttribute
{

    /**
     * @var string
     */
    private string $arrayFilterStyle;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $relevanceStatus;

    /**
     * @var bool
     */
    private bool $excludeAllOthers = false;

    /**
     * @var string
     */
    private string $otherReplaceText;

    /**
     * @var string
     */
    private string $randomOrder;

    /**
     * @var string
     */
    private string $alphaSort;

    /**
     * QuestionAttribute constructor.
     * @param string $name
     * @param string $arrayFilterStyle
     * @param bool $excludeAllOthers
     */
    public function __construct(string $name, string $arrayFilterStyle, string $otherReplaceText, string $randomOrder, string $alphaSort, bool $excludeAllOthers = false)
    {
        $this->name = $name;
        $this->arrayFilterStyle = $arrayFilterStyle;
        $this->otherReplaceText = $otherReplaceText;
        $this->randomOrder = $randomOrder;
        $this->alphaSort = $alphaSort;
        $this->excludeAllOthers = $excludeAllOthers;
    }

    /**
     * @return string
     */
    public function getArrayFilterStyle(): string
    {
        return $this->arrayFilterStyle;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRelevanceStatus(): string
    {
        return $this->relevanceStatus;
    }

    /**
     * @return bool
     */
    public function getExcludeAllOthers() : bool
    {
        return $this->excludeAllOthers;
    }

    /**
     * @return string
     */
    public function getOtherReplaceText(): string
    {
        return $this->otherReplaceText;
    }

    /**
     * @param $language
     * @return string
     */
    public function inLanguage($language): string
    {
        return $this->otherReplaceText[$language];
    }

    /**
     * @return string
     */
    public function getRandomOrder(): string
    {
        return $this->randomOrder;
    }

    /**
     * @return string
     */
    public function getAlphaSort() : string
    {
        return $this->alphaSort;
    }
}
