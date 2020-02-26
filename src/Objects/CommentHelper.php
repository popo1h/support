<?php

namespace Popo1h\Support\Objects;

class CommentHelper
{
    /**
     * 跨行注释(无法匹配中间包含*的注释)
     */
    const COMMENT_MATCH_TYPE_MULTI_LINE = 1;
    /**
     * 单行注释
     */
    const COMMENT_MATCH_TYPE_SINGLE_LINE = 2;

    /**
     * @var int
     */
    protected $commentMatchType;

    /**
     * @var string
     */
    protected $rawComment;

    /**
     * @var array
     */
    protected $commentLines;

    /**
     * @var array
     */
    protected $commentItemContentsList = [];

    /**
     * @param string $comment
     * @param int $commentMatchType
     */
    public function __construct($comment, $commentMatchType = self::COMMENT_MATCH_TYPE_MULTI_LINE)
    {
        $this->rawComment = $comment;
        $this->commentMatchType = $commentMatchType;
    }

    /**
     * @param string|object $class
     * @param int $commentMatchType
     * @return static
     * @throws \ReflectionException
     */
    public static function createByClass($class, $commentMatchType = self::COMMENT_MATCH_TYPE_MULTI_LINE)
    {
        $reflectionClass = new \ReflectionClass($class);
        $comment = $reflectionClass->getDocComment();

        return new static($comment, $commentMatchType);
    }

    /**
     * @param string|object $class
     * @param string $method
     * @param int $commentMatchType
     * @return static
     * @throws \ReflectionException
     */
    public static function createByMethod($class, $method, $commentMatchType = self::COMMENT_MATCH_TYPE_MULTI_LINE)
    {
        $reflectionMethod = new \ReflectionMethod($class, $method);
        $comment = $reflectionMethod->getDocComment();

        return new static($comment, $commentMatchType);
    }

    /**
     * @param string $function
     * @param int $commentMatchType
     * @return static
     * @throws \ReflectionException
     */
    public static function createByFunction($function, $commentMatchType = self::COMMENT_MATCH_TYPE_MULTI_LINE)
    {
        $reflectionFunction = new \ReflectionFunction($function);
        $comment = $reflectionFunction->getDocComment();

        return new static($comment, $commentMatchType);
    }

    /**
     * @return array
     */
    protected function getCommentList()
    {
        if (!isset($this->commentLines)) {
            $rawComment = str_replace('*\/', '*/', $this->rawComment);
            switch($this->commentMatchType){
                case self::COMMENT_MATCH_TYPE_MULTI_LINE:
                    preg_match_all('/\* ([^*]*?)$/ims', $rawComment, $commentMatchRes);
                    $this->commentLines = $commentMatchRes[1];
                    break;
                default:
                    preg_match_all('/\* (.*?)$/im', $rawComment, $commentMatchRes);
                    $this->commentLines = $commentMatchRes[1];
            }
        }

        return $this->commentLines;
    }

    /**
     * @param string $commentItemName
     * @return array
     */
    public function getCommentItemContents($commentItemName)
    {
        if (!isset($this->commentItemContentsList[$commentItemName])) {
            $this->commentItemContentsList[$commentItemName] = [];
            foreach ($this->getCommentList() as $commentLine) {
                if (preg_match('/@' . $commentItemName . '\s(.*?)$/ims', $commentLine, $commentLineMatchRes)) {
                    $this->commentItemContentsList[$commentItemName][] = $commentLineMatchRes[1];
                }
            }
        }

        return $this->commentItemContentsList[$commentItemName];
    }
}
