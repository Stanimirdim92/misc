<?php
declare(strict_types=1);

namespace Increate\Helpers;

use Locale;
use NumberFormatter;
use RuntimeException;
use function extension_loaded;
use function is_array;

/**
 * View helper for formatting currency.
 */
class CurrencyFormat
{
    /**
     * Locale to use instead of the default
     *
     * @var string
     */
    private string $locale;

    /**
     * CurrencyFormat constructor.
     *
     * @param string $locale
     */
    public function __construct(string $locale = '')
    {
        if (!extension_loaded('intl')) {
            throw new RuntimeException(sprintf(
                '%s component requires the intl PHP extension',
                __NAMESPACE__
            ));
        }

        $this->locale = $locale;
    }

    /**
     * @param float       $number
     * @param string|null $locale
     * @param int         $decimals
     * @param string|null $pattern
     *
     * @return bool|string
     */
    public function formatCurrency(
        float $number,
        string $locale = null,
        int $decimals = 2,
        string $pattern = null
    )
    {
        if (!$locale) {
            $locale = $this->getLocale();
        }

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        if ($pattern) {
            $formatter->setPattern($pattern);
        }

        $currencyCode = $formatter->getTextAttribute(NumberFormatter::CURRENCY_CODE);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);

        return $formatter->formatCurrency($number, $currencyCode);
    }

    /**
     * @param float       $number
     * @param string|null $locale
     * @param int|null    $decimals
     * @param int|null    $formatStyle
     * @param int|null    $formatType
     * @param array|null  $textAttributes
     *
     * @return bool|string
     */
    public function formatNumber(
        float $number,
        string $locale = null,
        int $decimals = null,
        int $formatStyle = null,
        int $formatType = null,
        array $textAttributes = null
    )
    {
        if (!$locale) {
            $locale = $this->getLocale();
        }

        if (!$formatStyle) {
            $formatStyle = NumberFormatter::DECIMAL;
        }

        if (!$formatType) {
            $formatType = NumberFormatter::TYPE_DOUBLE;
        }

        if (!$decimals) {
            $decimals = 2;
        }

        if (!is_array($textAttributes)) {
            $textAttributes = [];
        }

        $formatter = new NumberFormatter($locale, $formatStyle);

        if ($decimals > 0) {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);
            $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
            $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimals);
            $formatter->setAttribute(NumberFormatter::DECIMAL_ALWAYS_SHOWN, $decimals);
        }

        if($textAttributes) {
            foreach ($textAttributes as $textAttribute => $value) {
                $formatter->setTextAttribute($textAttribute, $value);
            }
        }

        $formattedValue = $formatter->parse((string)$formatter->format($number,$formatType), $formatType);

        if ($formattedValue === false) {
            throw new RuntimeException('Could not format number: ' . sprintf('%f', $number));
        }

        return number_format($formattedValue, $decimals, '.', '');
//        return $formattedValue;
    }

    /**
     * @param string $locale
     *
     * @return string
     */
    public function getCurrencyCode(string $locale) : string
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        $currencyCode = $formatter->getTextAttribute(NumberFormatter::CURRENCY_CODE);

        if($currencyCode === false) {
            throw new RuntimeException('Could not find currency code for locale: '.$locale);
        }

        return $currencyCode;
    }

    /**
     * Get the locale to use
     *
     * @return string
     */
    public function getLocale(): string
    {
        if (!$this->locale) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * @param string          $formattedNumber
     * @param NumberFormatter $formatter
     * @param string          $locale
     * @param string          $currencyCode
     *
     * @return string|null
     * @throws RuntimeException
     *
     * Special condition to be checked due to ICU bug:
     * http://bugs.icu-project.org/trac/ticket/10997
     *
     */
    private function fixICUBugForNoDecimals(string $formattedNumber, NumberFormatter $formatter, string $locale, string $currencyCode): ?string
    {
        $pattern = sprintf(
            '/\%s\d+(\s?%s)?$/u',
            $formatter->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL),
            preg_quote($this->getCurrencySymbol($locale, $currencyCode), '/')
        );

        return preg_replace($pattern, '$1', $formattedNumber);
    }

    /**
     * @param string $locale
     * @param string $currencyCode
     *
     * @return string
     * @throws RuntimeException
     */
    private function getCurrencySymbol(string $locale, string $currencyCode): string
    {
        $numberFormatter = new NumberFormatter($locale . '@currency=' . $currencyCode, NumberFormatter::CURRENCY);

        if (!$numberFormatter) {
            throw new RuntimeException('Could not create formatter for currency:' . sprintf('%s', $currencyCode));
        }

        $symbol = $numberFormatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL);

        if ($symbol) {
            return $symbol;
        }

        throw new RuntimeException('Could not determinate currency symbol.');
    }
}
