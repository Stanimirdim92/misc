<?php

/**
 * Provides a service to handle various date related functionality.
 *
 * @ingroup i18n
 */
class DateFormatter implements DateFormatterInterface
{
    public const FORMAT = 'Y-m-d H:i:s';

    /**
     * A RFC7231 Compliant date.
     *
     * http://tools.ietf.org/html/rfc7231#section-7.1.1.1
     *
     * Example: Sun, 06 Nov 1994 08:49:37 GMT
     */
    public const RFC7231 = 'D, d M Y H:i:s \\G\\M\\T';

    /**
     * An array of possible date parts.
     */
    protected static array $dateParts = [
        'year',
        'month',
        'day',
        'hour',
        'minute',
        'second',
    ];

    /**
     * The list of loaded timezones.
     *
     * @var array
     */
    protected array $timezones;

    /**
     * @var string|null
     */
    protected ?string $country;

    /**
     * @param int    $timestamp
     * @param string $type
     * @param string $format
     * @param null   $timezone
     * @param null   $langcode
     *
     * @return string
     * @throws Exception
     */
    public function format($timestamp, $type = 'medium', $format = '', $timezone = null, $langcode = null)
    {
        if (!isset($timezone)) {
            $timezone = date_default_timezone_get();
        }

        // Store DateTimeZone objects in an array rather than repeatedly
        // constructing identical objects over the life of a request.
        if (!isset($this->timezones[$timezone])) {
            $this->timezones[$timezone] = timezone_open($timezone);
        }

        if (empty($langcode)) {
            $langcode = 'en';
        }

        // Create a DrupalDateTime object from the timestamp and timezone.
        $create_settings = [
            'langcode' => $langcode,
            'country' => $this->country(),
        ];
        $date = $this->createFromTimestamp($timestamp, $this->timezones[$timezone], $create_settings);

        // If we have a non-custom date format use the provided date format pattern.
        if ($type !== 'custom') {
            if ($date_format = $this->dateFormat($type, $langcode)) {
                $format = $date_format->getPattern();
            }
        }

        // Fall back to the 'medium' date format type if the format string is
        // empty, either from not finding a requested date format or being given an
        // empty custom format string.
        if (empty($format)) {
            $format = $this->dateFormat('fallback', $langcode)->getPattern();
        }

        // Call $date->format().
        $settings = [
            'langcode' => $langcode,
        ];
        return $date->format($format, $settings);
    }

    /**
     * {@inheritdoc}
     */
    public function getSampleDateFormats($langcode = null, $timestamp = null, $timezone = null)
    {
        $timestamp = $timestamp ?: time();

        // All date format characters for the PHP date() function.
        $date_chars = mb_str_split('dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU');
        $date_elements = array_combine($date_chars, $date_chars);
        return array_map(function ($character) use ($timestamp, $timezone, $langcode) {
            return $this
                ->format($timestamp, 'custom', $character, $timezone, $langcode);
        }, $date_elements);
    }

    /**
     * {@inheritdoc}
     */
    public function formatTimeDiffUntil($timestamp, $options = [])
    {
        $request_time = $_SERVER['REQUEST_TIME'];
        return $this
            ->formatDiff($request_time, $timestamp, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function formatTimeDiffSince($timestamp, $options = [])
    {
        $request_time = $_SERVER['REQUEST_TIME'];
        return $this
            ->formatDiff($timestamp, $request_time, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function formatDiff($from, $to, $options = [])
    {
        $options += [
            'granularity' => 2,
            'langcode' => null,
            'strict' => true,
            'return_as_object' => false,
        ];
        if ($options['strict'] && $from > $to) {
            return '0 seconds';
        }
        $date_time_from = new DateTime();
        $date_time_from->setTimestamp($from);

        $date_time_to = new DateTime();
        $date_time_to->setTimestamp($to);

        $interval = $date_time_to->diff($date_time_from);
        $granularity = $options['granularity'];
        $output = '';

        // We loop over the keys provided by \DateInterval explicitly. Since we
        // don't take the "invert" property into account, the resulting output value
        // will always be positive.
        $max_age = 1.0E+99;
        foreach ([
                     'y',
                     'm',
                     'd',
                     'h',
                     'i',
                     's',
                 ] as $value) {
            if ($interval->{$value} > 0) {

                // Switch over the keys to call formatPlural() explicitly with literal
                // strings for all different possibilities.
                switch ($value) {
                    case 'y':
                        $interval_output = $interval->y;
                        $max_age = min($max_age, 365 * 86400);
                        break;
                    case 'm':
                        $interval_output = $interval->m;
                        $max_age = min($max_age, 30 * 86400);
                        break;
                    case 'd':

                        // \DateInterval doesn't support weeks, so we need to calculate them
                        // ourselves.
                        $interval_output = '';
                        $days = $interval->d;
                        $weeks = floor($days / 7);
                        if ($weeks) {
                            $interval_output .= $weeks;
                            $days -= $weeks * 7;
                            $granularity--;
                            $max_age = min($max_age, 7 * 86400);
                        }
                        if ((!$output || $weeks > 0) && $granularity > 0 && $days > 0) {
                            $interval_output .= ($interval_output ? ' ' : '') . $days;
                            $max_age = min($max_age, 86400);
                        } else {

                            // If we did not output days, set the granularity to 0 so that we
                            // will not output hours and get things like "1 week 1 hour".
                            $granularity = 0;
                        }
                        break;
                    case 'h':
                        $interval_output = $interval->h;
                        $max_age = min($max_age, 3600);
                        break;
                    case 'i':
                        $interval_output = $interval->i;
                        $max_age = min($max_age, 60);
                        break;
                    case 's':
                        $interval_output = $interval->s;
                        $max_age = min($max_age, 1);
                        break;
                }
                $output .= ($output && $interval_output ? ' ' : '') . $interval_output;
                $granularity--;
            } elseif ($output) {

                // Break if there was previous output but not any output at this level,
                // to avoid skipping levels and getting output like "1 year 1 second".
                break;
            }
            if ($granularity <= 0) {
                break;
            }
        }

        if (empty($output)) {
            $output = '0 seconds';
            $max_age = 0;
        }

        return $output;
    }

    /**
     * Returns the default country from config.
     *
     * @return string
     *   The config setting for country.default.
     */
    protected function country()
    {
        if ($this->country === null) {
            $this->country = 'UK'; // todo: add from config
        }

        return $this->country;
    }

    /**
     * @param       $timestamp
     * @param null  $timezone
     * @param array $settings
     *
     * @return DateTime|null
     * @throws Exception
     */
    public function createFromTimestamp($timestamp, $timezone = null, $settings = []): ?DateTime
    {
        if (!is_numeric($timestamp)) {
            throw new \Exception('The timestamp must be numeric.');
        }

        $datetime = $this->prepareteDateFormat('', $timezone, $settings);

        if($datetime) {
            $datetime->setTimestamp($timestamp);
        }

        return $datetime;
    }

    /**
     * Prepares the input timezone value.
     *
     * Changes the timezone before trying to use it, if necessary.
     * Most importantly, makes sure there is a valid timezone
     * object before moving further.
     *
     * @param mixed $timezone
     *   Either a timezone name or a timezone object or NULL.
     *
     * @return DateTimeZone
     *   The massaged time zone.
     */
    protected function prepareTimezone($timezone): DateTimeZone
    {
        // If the input timezone is a valid timezone object, use it.
        if ($timezone instanceof DateTimeZone) {
            $timezone_adjusted = $timezone;
        } elseif (!empty($timezone) && is_string($timezone)) {
            $timezone_adjusted = new DateTimeZone($timezone);
        }

        // Default to the system timezone when not explicitly provided.
        // If the system timezone is missing, use 'UTC'.
        if (!$timezone_adjusted || !$timezone_adjusted instanceof DateTimeZone) {
            $system_timezone = date_default_timezone_get();
            $timezone_name = !empty($system_timezone) ? $system_timezone : 'UTC';
            $timezone_adjusted = new DateTimeZone($timezone_name);
        }

        // We are finally certain that we have a usable timezone.
        return $timezone_adjusted;
    }

    /**
     * Constructs a date object set to a requested date and timezone.
     *
     * @param string $time
     *   (optional) A date/time string. Defaults to 'now'.
     * @param mixed  $timezone
     *   (optional) \DateTimeZone object, time zone string or NULL. NULL uses the
     *   default system time zone. Defaults to NULL.
     * @param array  $settings
     *   (optional) Keyed array of settings. Defaults to empty array.
     *   - langcode: (optional) String two letter language code used to control
     *     the result of the format(). Defaults to NULL.
     *   - debug: (optional) Boolean choice to leave debug values in the
     *     date object for debugging purposes. Defaults to FALSE.
     */
    public function prepareteDateFormat($time = 'now', $timezone = null, $settings = []): ?DateTime
    {
        $errors = [];
        $dateTimeObject = null;

        // Massage the input values as necessary.
        $prepared_time = $time;
        $prepared_timezone = $this->prepareTimezone($timezone);
        try {
            if (!empty($prepared_time)) {
                $test = date_parse($prepared_time);
                if ($test && !empty($test['errors'])) {
                    $errors[] = $test['errors'];
                }
            }
            if (empty($test)) {
                $dateTimeObject = new DateTime($prepared_time, $prepared_timezone);
            }
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        // Clean up the error messages.
        $errors = array_unique($this->checkErrors($errors));

        return $dateTimeObject;
    }

    /**
     * Examines getLastErrors() to see what errors to report.
     *
     * Two kinds of errors are important: anything that DateTime
     * considers an error, and also a warning that the date was invalid.
     * PHP creates a valid date from invalid data with only a warning,
     * 2011-02-30 becomes 2011-03-03, for instance, but we don't want that.
     *
     * @see http://php.net/manual/time.getlasterrors.php
     */
    public function checkErrors(array $err = [])
    {
        $errors = DateTime::getLastErrors();
        if (!empty($errors['errors'])) {
            $err += $errors['errors'];
        }

        // Most warnings are messages that the date could not be parsed
        // which causes it to be altered. For validation purposes, a warning
        // as bad as an error, because it means the constructed date does
        // not match the input value.
        if (!empty($errors['warnings'])) {
            $err[] = 'The date is invalid.';
        }

        return $err;
    }

}
