<?php

namespace Popo1h\Support\Traits\Errors;

trait ClassErrorsTrait
{
    /**
     * 最近一次错误码
     * @var mixed
     */
    protected $_classErrorErrorCode;

    /**
     * 最近一次错误信息
     * @var string
     */
    protected $_classErrorErrorInfo;

    /**
     * 错误信息数组
     * @var array
     */
    protected $_classErrorErrors;

    /**
     * 默认错误信息
     * @var string
     */
    protected $_classErrorDefaultSuccessInfo = '操作成功';

    /**
     * 默认错误信息
     * @var string
     */
    protected $_classErrorDefaultErrorInfo = '操作失败';

    /**
     * 初始化错误信息
     */
    protected function initErrors()
    {
        $this->_classErrorErrors = [];
    }

    /**
     * 清除错误
     */
    public function clearError()
    {
        $this->_classErrorErrorCode = null;
        $this->_classErrorErrorInfo = null;
    }

    /**
     * 设置错误
     * @param mixed $errorCode 错误码
     * @param string $errorInfo 错误信息
     */
    protected function setError($errorCode, $errorInfo = null)
    {
        $this->_classErrorErrorCode = [
            'class' => $this,
            'code' => $errorCode,
        ];
        $this->_classErrorErrorInfo = $errorInfo;
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
        return $this->_classErrorErrorCode;
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

    public function getErrorCodeAndClass()
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
        return $this->_classErrorErrorInfo;
    }

    /**
     * 根据错误码获取错误信息
     * @param string $errorCode 错误码
     * @return string
     */
    public function getErrorInfoByCode($errorCode)
    {
        if ($this->checkError() == false) {
            return $this->_classErrorDefaultSuccessInfo;
        }

        if (!isset($this->_classErrorErrors)) {
            $this->initErrors();
        }

        if (isset($this->_classErrorErrors[$errorCode])) {
            return $this->_classErrorErrors[$errorCode];
        } else {
            return $this->_classErrorDefaultErrorInfo;
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
                return $this->_classErrorDefaultSuccessInfo;
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
