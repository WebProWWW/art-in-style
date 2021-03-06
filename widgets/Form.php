<?php
/**
 * Author: Timur Valiev
 * Site: https://webprowww.github.io
 * 2019-02-04 17:48
 */

namespace widgets;

use Yii;
use yii\base\Widget;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\Json;

/**
 * Class Form
 * @package widgets
 *
 * @property Model $model
 */
class Form extends Widget
{

    public $model;
    public $action = null;
    public $method = 'post';
    public $opt = [];

    public $ajax = false;
    public $ajaxOnSuccess;
    public $ajaxOnError;
    public $ajaxOnErrorValidate;

    const AJAX_POPUP_SUCCESS = 'popupSuccess';
    const AJAX_POPUP_ERROR = 'popupError';
    const AJAX_ADD_SUCCESS = 'addSuccess';
    const AJAX_ADD_ERROR = 'addError';
    const AJAX_REDIRECT = 'redirect';
    const AJAX_REFRESH = 'refresh';
    const AJAX_ADD_ERRORS = 'addErrors';

    public function init()
    {
        parent::init();
        ob_start();
        if ($this->ajax) {
            if ($this->ajaxOnSuccess === null) $this->ajaxOnSuccess = self::AJAX_POPUP_SUCCESS;
            if ($this->ajaxOnError === null) $this->ajaxOnError = self::AJAX_POPUP_ERROR;
            if ($this->ajaxOnErrorValidate === null) $this->ajaxOnErrorValidate = self::AJAX_ADD_ERRORS;
            echo Html::beginForm(null, $this->method, ArrayHelper::merge([
                'data-ajax' => Json::encode([
                    'action' => Url::to($this->action),
                    'onSuccess' => $this->ajaxOnSuccess,
                    'onError' => $this->ajaxOnError,
                    'onErrorValidate' => $this->ajaxOnErrorValidate,
                ]),
            ], $this->opt));
        } else {
            echo Html::beginForm($this->action, $this->method, $this->opt);
        }
    }

    public static function success()
    {
        return ['formValidateStatus' => 1];
    }

    public static function validate($model)
    {
        return [
            'formValidateStatus' => 0,
            'errors' => ActiveForm::validate($model),
        ];
    }

    /**
     * @return false|string
     */
    public function run()
    {
        echo Html::endForm();
        return ob_get_clean();
    }

    /**
     * @param string $attr
     * @param array $opt
     * @return string
     */
    public function label($attr, $opt=[])
    {
        return Html::activeLabel($this->model, $attr, ArrayHelper::merge([
            'class' => 'label',
        ], $opt));
    }

    /**
     * @param string $attr
     * @param array $opt
     * @return string
     */
    public function error($attr, $opt=[])
    {
        try {
            return Html::tag('div', $this->model->getFirstError($attr), ArrayHelper::merge([
                'class' => 'input-error',
                'for' => $this->getInputId($attr),
            ], $opt));
        } catch (Exception $exception) {
            return '';

        }
    }

    /**
     * @param string $type
     * @param string $attr
     * @param array $opt
     * @return string
     */
    public function input($type, $attr, $opt=[])
    {
        return Html::activeInput($type, $this->model, $attr, ArrayHelper::merge([
            'class' => 'input',
            'placeholder' => true
        ], $opt));
    }

    /**
     * @param string $attr
     * @param array $opt
     * @return string
     */
    public function inputText($attr, $opt=[])
    {
        return $this->input('text', $attr, $opt);
    }

    /**
     * @param string $attr
     * @param array $opt
     * @return string
     */
    public function inputPassword($attr, $opt=[])
    {
        return $this->input('password', $attr, $opt);
    }

    public function textarea($attr, $opt=[])
    {
        return Html::activeTextarea($this->model, $attr, ArrayHelper::merge([
            'class' => 'input',
            'rows' => 3,
            'placeholder' => true,
        ], $opt));
    }

    /**
     * @param string $attr
     * @param array $opt keys: label, input, text
     * @return string
     */
    public function checkbox($attr, $opt=[])
    {
        $content = Html::activeCheckbox($this->model, $attr, ArrayHelper::merge([
            'class' => 'checkbox',
            'label' => false,
        ], ArrayHelper::getValue($opt, 'input', [])))
        .Html::tag('span', $this->model->getAttributeLabel($attr), ArrayHelper::merge([
            'class' => 'checkbox-text'
        ], ArrayHelper::getValue($opt, 'text', [])));
        return Html::label($content, false, ArrayHelper::merge([
            'class' => 'checkbox-label',
        ], ArrayHelper::getValue($opt, 'label', [])));
    }

    /**
     * @param string $attr
     * @param array $items
     * @param array $opt
     * @return string
     */
    public function dropdown($attr, $items=[], $opt=[])
    {
        return Html::activeDropDownList($this->model, $attr, $items, ArrayHelper::merge([
            'class' => 'input',
        ], $opt));
    }

    /**
     * @param string $content
     * @param array $opt
     * @return string
     */
    public function submit($content, $opt=[])
    {
        return Html::submitButton($content, ArrayHelper::merge([
            'class' => 'btn btn-submit js-ajax-loading'
        ], $opt));
    }

    /**
     * @param string $attr
     * @param array $opt keys: label, input, error
     * @return string
     */
    public function inputTextWithLabelError($attr, $opt=[])
    {
        return ''
            .$this->label($attr, ArrayHelper::getValue($opt, 'label', []))
            .$this->inputText($attr, ArrayHelper::merge([
                'placeholder'=>false,
            ], ArrayHelper::getValue($opt, 'input', [])))
            .$this->error($attr, ArrayHelper::getValue($opt,'error', []))
        .'';
    }

    /**
     * @param string $attr
     * @param array $opt keys: label, input, error
     * @return string
     */
    public function textareaWithLabelError($attr, $opt=[])
    {
        return ''
            .$this->label($attr, ArrayHelper::getValue($opt, 'label', []))
            .$this->textarea($attr, ArrayHelper::merge([
                'placeholder'=>false
            ],ArrayHelper::getValue($opt, 'input', [])))
            .$this->error($attr, ArrayHelper::getValue($opt,'error', []))
        .'';
    }

    /**
     * @param string $attr
     * @param array $opt keys: input, error
     * @return string
     */
    public function inputTextWithError($attr, $opt=[])
    {
        return ''
            .$this->input('text', $attr, ArrayHelper::getValue($opt, 'input', []))
            .$this->error($attr, ArrayHelper::getValue($opt, 'error', []))
        .'';
    }

    /**
     * @param string $attr
     * @param array $opt keys: input, error
     * @return string
     */
    public function inputPasswordWithError($attr, $opt=[])
    {
        return ''
            .$this->input('password', $attr, ArrayHelper::getValue($opt, 'input', []))
            .$this->error($attr, ArrayHelper::getValue($opt, 'error', []))
        .'';
    }

    /**
     * @param string $attr
     * @param array $opt
     * @return string
     */
    public function inputPasswordWithLabelError($attr, $opt=[])
    {
        return ''
            .$this->label($attr, ArrayHelper::getValue($opt, 'label', []))
            .$this->inputPassword($attr, ArrayHelper::merge([
                'placeholder' => false,
            ], ArrayHelper::getValue($opt, 'input', [])))
            .$this->error($attr, ArrayHelper::getValue($opt, 'error', []))
        .'';
    }

    /**
     * @param string $attr
     * @param array $items
     * @param array $opt
     * @return string
     */
    public function dropdownWithLabelError($attr, $items=[], $opt=[])
    {
        return ''
            .$this->label($attr, ArrayHelper::getValue($opt, 'label', []))
            .$this->dropdown($attr, $items, ArrayHelper::getValue($opt, 'input', []))
            .$this->error($attr, ArrayHelper::getValue($opt,'error', []))
        .'';
    }

    /**
     * @param string $attr
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getInputId($attr)
    {
        return strtolower($this->model->formName().'-'.$attr);
    }

    /**
     * @param string $attr
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getInputName($attr)
    {
        return $this->model->formName().'['.$attr.']';
    }

}