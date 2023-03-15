<?php

use Carbon\Carbon;
use voku\helper\StopWords;

if (!function_exists('translations')) {
    function translations($json)
    {
        if (!file_exists($json)) {
            return [];
        }
        return json_decode(file_get_contents($json), true);
    }
}

function getUiPathOrchestratorEndpoint($hostingType, $url)
{
    $endpoint = '';
    if ($hostingType === 'on_premise') {
        // build on premise identity server token endpoint (url parameter must be filled)
        $endpoint = sprintf(
            config('constants.uipath.orchestrator.identity_server_token_endpoint.on_premise'),
            $url
        );
    } elseif ($hostingType === 'cloud') {
        // build cloud identity server token endpoint
        $endpoint = sprintf(
            config('constants.uipath.orchestrator.identity_server_token_endpoint.cloud'),
            config('constants.uipath.orchestrator.cloud.url_prefix')
        );
    }
    return $endpoint;
}

function getAcronym($string, $minlength = 2, $maxlength = 5)
{
    $stopWords = new StopWords();
    $localeStopWords = $stopWords->getStopWordsFromLanguage(app()->getLocale());
    $englishStopWords = $stopWords->getStopWordsFromLanguage('en');

    $acronym = '';

    // replace consecutive spaces with single space
    $string = preg_replace('!\s+!', ' ', $string);
    $string = str_replace('-', ' ', $string);

    // clean the name from stop words
    $cleanedName = array_diff(explode(' ', strtolower($string)), $localeStopWords);
    $cleanedName = array_diff($cleanedName, $englishStopWords);

    // if string is empty after clean, restore it
    if (count($cleanedName) == 0) {
        $cleanedName = explode(' ', strtolower($string));
    }

    // build acronym
    foreach ($cleanedName as $word) {
        $acronym .= strtoupper(substr($word, 0, 1));
        if (strlen($acronym) == $maxlength) {
            break;
        }
    }

    // if the acronym's length is < $minlength, add the following character of first word in original string
    while (strlen($acronym) < $minlength && strlen($cleanedName[0]) > strlen($acronym)) {
        $acronym .= strtoupper(substr($cleanedName[0], strlen($acronym), 1));
    }

    return $acronym;
}

/**
 * Round minutes to the nearest interval of a DateTime object.
 * 
 * @param Carbon $dateTime
 * @param int $minuteInterval
 * @return Carbon
 */
function roundToNearestMinuteInterval(Carbon $dateTime, $minuteInterval = 15)
{
    return $dateTime->setTime(
        $dateTime->format('H'),
        round($dateTime->format('i') / $minuteInterval) * $minuteInterval,
        0
    );
}

function createToast($message, $style)
{
    session()->flash('toast', $message);
    session()->flash('toastStyle', $style);
    session()->flash('toastId', fake(app()->getLocale())->uuid());
}