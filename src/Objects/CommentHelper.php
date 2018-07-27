<?php

namespace Popo1h\Support\Objects;

class CommentHelper
{
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
     */
    public function __construct($comment)
    {
        $this->rawComment = $comment;
    }

    /**
     * @param string|object $class
     * @return static
     * @throws \ReflectionException
     */
    public static function createByClass($class)
    {
        $reflectionClass = new \ReflectionClass($class);
        $comment = $reflectionClass->getDocComment();

        return new static($comment);
    }

    /**
     * @param string|object $class
     * @param string $method
     * @return static
     * @throws \ReflectionException
     */
    public static function createByMethod($class, $method)
    {
        $reflectionMethod = new \ReflectionMethod($class, $method);
        $comment = $reflectionMethod->getDocComment();

        return new static($comment);
    }

    /**
     * @param string $function
     * @return static
     * @throws \ReflectionException
     */
    public static function createByFunction($function)
    {
        $reflectionFunction = new \ReflectionFunction($function);
        $comment = $reflectionFunction->getDocComment();

        return new static($comment);
    }

    /**
     * @return array
     */
    protected function getCommentList()
    {
        if (!isset($this->commentLines)) {
            preg_match_all('/\* ([^*]*?)$/ims', $this->rawComment, $commentMatchRes);
            $this->commentLines = $commentMatchRes[1];
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
