<?php

namespace app\components\formatters;

use yii\helpers\Html;
use yii\i18n\Formatter;

/**
 * Class Formatter
 */
class PhoneFormatter extends Formatter
{
    /**
     * Дефолтное значение кода страны номера
     *
     * @var string
     */
    private string $defaultPhoneCode = 'RU';


    /**
     * @param $phone
     * @return string|string[]|null
     */
    public function asPhoneDB($phone)
    {
        //"+7 (495) 555-55-55", "8495 555 5555", "555-5555" — все к виду  "74955555555"
        $phone = preg_replace('/\D+/', '', $phone);
        $strlen = strlen($phone);
        if ($strlen === 7) {
            $phone = "7495{$phone}";
        } elseif ($strlen === 10) {
            $phone = "7{$phone}";
        } elseif ($phone[0] === 8 && $strlen === 11) {
            $phone = '7' . (substr($phone, 1));
        }
        return $phone;
    }

    /**
     * Форматирование номера телефона
     * Эта функция может принимать 11-значный, 10-значный, 7-значный или 6-значный номер
     * телефона и
     * возвращает
     *
     * @param int $number Номер телефона, который будет отформатирован
     *
     * @param string $code Код страны, по умолчанию Россия (RU -> +7)
     * @param bool $link Выводить телефон в виде HTML ссылки
     * @param array $options Опции для HTML ссылки
     *
     * @return string
     */
    public function asPhone(int $number, $code = 'RU', $link = true, array $options = []): string
    {
        if ($number === null) {
            return $this->nullDisplay;
        } else {
            return $this->formatPhone($number, $code, $link, $options);
        }
    }

    /**
     * Функция форматирования
     *
     * @param $number
     * @param string $code
     * @param bool $link
     * @param array $options
     *
     * @return string
     */
    private function formatPhone($number, string $code, bool $link, array $options): string
    {
        $number = preg_replace("/\D/", "", $number);

        if (strlen($number) === 6) {
            $number = preg_replace("/(\d{3})(\d{3})/", "$1-$2", $number);
        } else if (strlen($number) === 7) {
            $number = preg_replace("/(\d{3})(\d{4})/", "$1-$2", $number);
        } else if (strlen($number) === 10) {
            $number = preg_replace("/(\d{3})(\d{3})(\d{2})(\d{2})/", "($1) $2-$3-$4", $number);
        } else if (strlen($number) === 11) {
            $number = preg_replace("/(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})/", "$1 ($2) $3-$4-$5", $number);
        }

        $number = $this->getCodeCountryByIso($code) . ' ' . $number;

        if ($link === false) {
            return $number;
        } else {
            $url = $this->buildUrlPhone($number);
            return Html::a($number, $url, $options);
        }
    }

    /**
     * Получаем код страны телефона, по умолчанию  RU => +7
     * Реализована только россия
     *
     * @param $code
     *
     * @return null|string
     */
    private function getCodeCountryByIso($code): ?string
    {
        if ($code === null) {
            $code = $this->defaultPhoneCode;
        }

        if ($code === 'RU') {
            return '+7';
        }
        return null;
    }

    /**
     * Строит телефонную ссылку из передаваемой строки
     * Передаваемая строка может быть числом, отформатированным числом или номером телефона.
     *
     * @param string $url The number or tel url to use in the link
     *
     * @return string rfc3966 formatted tel URL
     */
    private function buildUrlPhone(string $url): string
    {
        $number = preg_replace("/\D+/", "", $url);
        return "tel:+" . $number;
    }
}
