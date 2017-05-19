<?php

namespace Popo1h\Support\Traits;

trait ClassErrors
{
    /**
     * 最近一次错误码
     * @var mixed
     */
    protected $_errorCode;

    /**
     * 最近一次错误信息
     * @var string
     */
    protected $_errorInfo;

    /**
     * 错误信息数组
     * @var array
     */
    protected $_errors;

    /**
     * 默认错误信息
     * @var string
     */
    protected $_defaultSuccessInfo = '操作成功';

    /**
     * 默认错误信息
     * @var string
     */
    protected $_defaultErrorInfo = '操作失败';

    /**
     * 初始化错误信息
     */
    protected function initErrors()
    {
        $this->_errors = [];
    }

    /**
     * 清除错误
     */
    public function clearError()
    {
        $this->_errorCode = null;
        $this->_errorInfo = null;
    }

    /**
     * 设置错误
     * @param mixed $errorCode 错误码
     * @param string $errorInfo 错误信息
     */
    protected function setError($errorCode, $errorInfo = null)
    {
        $this->_errorCode = [
            'class' => $this,
            'code' => $errorCode,
        ];
        $this->_errorInfo = $errorInfo;
    }

    /**
     * 使用其他类进行设置错误
     * @param object $otherClass
     */
    protected function setErrorByOtherClass($otherClass)
    {
        if ($otherClass->checkError() != false) {
            $this->setError($otherClass->getRawErrorCode(), $otherClass->getErrorInfo());
        }
    }

    /**
     * 获取原始授权码
     * @return mixed
     */
    public function getRawErrorCode()
    {
        return $this->_errorCode;
    }

    public function getErrorCode()
    {
        $rawErrorCode = $this->getRawErrorCode();

        $errorCode = '';
        while ($rawErrorCode) {
            if (!empty($errorCode)) {
                $errorCode .= '-';
            }
            $errorCode .= get_class($rawErrorCode['class']);
            if (!is_array($rawErrorCode['code'])) {
                $errorCode .= ':' . $rawErrorCode['code'];
                break;
            } else {
                $rawErrorCode = $rawErrorCode['code'];
            }
        }

        return $errorCode;
    }

    protected function getErrorCodeAndClass()
    {
        $rawErrorCode = $this->getRawErrorCode();

        $result = [];
        while ($rawErrorCode) {
            if (!is_array($rawErrorCode['code'])) {
                $result = [
                    'class' => $rawErrorCode['class'],
                    'code' => $rawErrorCode['code'],
                ];
                break;
            } else {
                $rawErrorCode = $rawErrorCode['code'];
            }
        }

        return $result;
    }

    /**
     * 获取原始错误信息
     * @return string
     */
    public function getRawErrorInfo()
    {
        return $this->_errorInfo;
    }

    /**
     * 根据错误码获取错误信息
     * @param string $errorCode 错误码
     * @return string
     */
    public function getErrorInfoByCode($errorCode)
    {
        if ($this->checkError() == false) {
            return $this->_defaultSuccessInfo;
        }

        if (!isset($this->_errors)) {
            $this->initErrors();
        }

        if (isset($this->_errors[$errorCode])) {
            return $this->_errors[$errorCode];
        } else {
            return $this->_defaultErrorInfo;
        }
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getErrorInfo()
    {
        $errorInfo = $this->getRawErrorInfo();
        if (isset($errorInfo)) {
            return $errorInfo;
        } else {
            $errorCodeArr = $this->getErrorCodeAndClass();

            if (!isset($errorCodeArr['code'])) {
                return $this->_defaultSuccessInfo;
            }
            return call_user_func([$errorCodeArr['class'], 'getErrorInfoByCode'], $errorCodeArr['code']);
        }
    }

    /**
     * 验证是否报错
     * @return bool
     */
    public function checkError()
    {
        if ($this->getErrorCodeAndClass()) {
            return true;
        } else {
            return false;
        }
    }
}
