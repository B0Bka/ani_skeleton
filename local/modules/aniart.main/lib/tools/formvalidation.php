<?
/**
 * Валидация форм авторизации, регистрации и
 * восстановления пароля
 */

namespace Aniart\Main\Tools;

use Aniart\Main\Exceptions\AniartException,
    Aniart\Main\Ext\User;

class FormValidation
{
    private $arRequiredRegister = ['EMAIL', 'NAME', 'LAST_NAME', 'PASSWORD', 'CONFIRM_PASSWORD', 'PHONE'],
        $arRequiredLogin = ['LOGIN', 'PASSWORD'],
        $arRequiredForgot = ['EMAIL'],
        $arRequiredProfile = ['EMAIL', 'NAME', 'LAST_NAME', 'PHONE'],
        $arRequiredFeedback = ['EMAIL', 'NAME'],
        $minPassLength = 6;
    protected $data, $type;

    function  __construct($arData, $type)
    {
        $this->data = $arData;
        $this->type = $type;
    }

    public function checkValidation()
    {
        if(!$this->type){
            throw new AniartException("Не указан тип валидации");
        }
        if($this->type == 'auth') return $this->validateAuth();
        elseif($this->type == 'register') return $this->validateRegister();
        elseif($this->type == 'forgot') return $this->validateForgot();
        elseif($this->type == 'profile') return $this->validateProfile();
        elseif($this->type == 'feedback') return $this->validateFeedback();
    }

    /**
     * Валидация формы авторизации
     */
    private function validateAuth()
    {
        return $this->getRequired($this->arRequiredLogin, 'auth');
    }

    /**
     * Валидация формы регистрации
     */
    private function validateRegister()
    {

        $arError = [];
        $arRequired = $this->getRequired($this->arRequiredRegister, 'auth');

        $this->data['recaptcha'];
        if (!isset($this->data['recaptcha']) || strlen($this->data['recaptcha']) <= 0) {
            $arError['recaptcha'] = i18n('RECAPTCHA_EMPTY', 'auth');
        } else {
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
                'secret'=> GRECAPTCHA_KEY_PRIVATE,
                'response'=> $this->data['recaptcha']
            )));
            $out = curl_exec($ch);
            $result = json_decode($out);
            if($result->success == false){
                $arError['recaptcha'] = i18n('RECAPTCHA_WRONG', 'auth');;
                $arError['recaptcha_update'] = 1;
            }
        }

        if($this->data['PASSWORD'] != $this->data['CONFIRM_PASSWORD'] && strlen($this->data['CONFIRM_PASSWORD']) > 0)
            $arError['CONFIRM_PASSWORD'] = i18n('ERROR_DIFFRENT_PASSWORD', 'auth');
        if(strlen($this->data['PASSWORD']) > 0 && !$this->checkPass($this->data['PASSWORD']))
            $arError['PASSWORD'] = i18n('ERROR_SHORT_PASSWORD', 'auth');
        if(!$this->checkEmail($this->data['EMAIL']) && strlen($this->data['EMAIL']) > 0)
            $arError['EMAIL'] = i18n('ERROR_EMAIL', 'auth');
        if(!empty(User::ExistsByEmail($this->data['EMAIL'])) && strlen($this->data['EMAIL']) > 0) {
            $usr = array_shift(User::ExistsByEmail($this->data['EMAIL']));
            $arError['EMAIL'] = $usr["NAME"] . ", вы уже зарегистрированы. Чтобы оформить заказ - авторизуйтесь ".
                htmlspecialchars_decode("<span>Здесь</span>") ;
        }
        if(!checkPhone($this->data['PHONE']) && strlen($this->data['PHONE']) > 0) $arError['PHONE'] = i18n('ERROR_PHONE', 'auth');

        if(count($arRequired) > 0 && count($arError) > 0) $arError = array_merge($arRequired, $arError);
        elseif(count($arRequired) > 0) $arError = $arRequired;

        return (count($arError) > 0) ? $arError : false;
    }

    /**
     * Валидация форм восстановления пароля
     */
    private function validateForgot()
    {
        $arError = [];
        $arRequired = $this->getRequired($this->arRequiredForgot, 'auth');

        if(!$this->checkEmail($this->data['EMAIL']) && strlen($this->data['EMAIL']) > 0)
            $arError['EMAIL'] = i18n('ERROR_EMAIL', 'auth');
        if(!User::ExistsByEmail($this->data['EMAIL']) && strlen($this->data['EMAIL']) > 0)
            $arError['EMAIL'] = i18n('ERROR_EMAIL_NOT_FOUND', 'auth');

        if(count($arRequired) > 0 && count($arError) > 0) $arError = array_merge($arRequired, $arError);
        elseif(count($arRequired) > 0) $arError = $arRequired;

        return (count($arError) > 0) ? $arError : false;
    }

    /**
     * Валидация редактирования профиля
     */
    private function validateProfile()
    {
        $arError = [];
        $arRequired = $this->getRequired($this->arRequiredProfile, 'auth');
        if(empty($this->data['CONFIRM_PASSWORD']) && strlen($this->data['PASSWORD']) > 0)
            $arError['CONFIRM_PASSWORD'] = i18n('ERROR_EMPTY_CONFIRM_PASSWORD', 'auth');
        if($this->data['PASSWORD'] != $this->data['CONFIRM_PASSWORD'] && strlen($this->data['CONFIRM_PASSWORD']) > 0)
            $arError['CONFIRM_PASSWORD'] = i18n('ERROR_DIFFRENT_PASSWORD', 'auth');
        if(strlen($this->data['PASSWORD']) > 0 && !$this->checkPass($this->data['PASSWORD']))
            $arError['PASSWORD'] = i18n('ERROR_SHORT_PASSWORD', 'auth');
        if(!$this->checkEmail($this->data['EMAIL']) && strlen($this->data['EMAIL']) > 0)
            $arError['EMAIL'] = i18n('ERROR_EMAIL', 'auth');
        if(!checkPhone($this->data['PHONE']) && strlen($this->data['PHONE']) > 0) $arError['PHONE'] = i18n('ERROR_PHONE', 'auth');

        if(count($arRequired) > 0 && count($arError) > 0) $arError = array_merge($arRequired, $arError);
        elseif(count($arRequired) > 0) $arError = $arRequired;

        return (count($arError) > 0) ? $arError : false;
    }

    /**
     * Валидация формы обратной связи
     */
    private function validateFeedback()
    {
        $arError = [];
        $arRequired = $this->getRequired($this->arRequiredFeedback, 'feedback');

        if(!$this->checkEmail($this->data['EMAIL']) && strlen($this->data['EMAIL']) > 0)
            $arError['EMAIL'] = i18n('ERROR_EMAIL', 'auth');

        if(count($arRequired) > 0 && count($arError) > 0) $arError = array_merge($arRequired, $arError);
        elseif(count($arRequired) > 0) $arError = $arRequired;

        return (count($arError) > 0) ? $arError : false;
    }
    /**
     * Проверка email
     */
    public function checkEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Длина пароля
     */
    public function checkPass($pass)
    {
        return (strlen($pass) >= $this->minPassLength);
    }

    /**
     * Обязательные поля
     */
    private function getRequired($arRequired, $group)
    {
        $error = [];

        foreach($arRequired as $req)
        {
            if(strlen($this->data[$req]) <= 0)  $error[$req] = i18n('ERROR_EMPTY_'.$req, $group);
        }
        return $error;
    }
}
