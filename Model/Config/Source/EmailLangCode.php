<?php

namespace DWD\KiyohReviews\Model\Config\Source;

use \Magento\Framework\Data\OptionSourceInterface;

class EmailLangCode implements OptionSourceInterface
{
    /*
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];
        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $value,
                'label' => $key
            ];
        }
        return $ret;
    }

    /*
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        return [
            'English' => 'en',
            'Nederlands' => 'nl',
            'Suomalainen' => 'fi-FI',
            'Français' => 'fr',
            'Vlaams' => 'be',
            'German' => 'de',
            'Hungarian' => 'hu',
            'Bulgarian' => 'bg',
            'Romanian' => 'ro',
            'Croatian' => 'hr',
            'Japanese' => 'ja',
            'Spanish' => 'es-ES',
            'Italian' => 'it',
            'Portuguese' => 'pt-PT',
            'Turkish' => 'tr',
            'Norwegian' => 'nn-NO',
            'Swedish' => 'sv-SE',
            'Danish' => 'da',
            'Brazilian Portuguese' => 'pt-BR',
            'Polish' => 'pl',
            'Slovenian' => 'sl',
            'Chinese' => 'zh-CN',
            'Russian' => 'ru',
            'Greek' => 'el',
            'Czech' => 'cs',
            'Estonian' => 'et',
            'Lithuanian' => 'lt',
            'Latvian' => 'lv',
            'Sloviak' => 'sk'
        ];
    }
}




