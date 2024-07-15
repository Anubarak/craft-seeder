<?php
/**
 * Craft CMS Plugins
 *
 * Created with PhpStorm.
 *
 * @link      https://github.com/Anubarak/
 * @email     anubarak1993@gmail.com
 * @copyright Copyright (c) 2024 Robin Schambach|Secondred Newmedia GmbH
 */

namespace anubarak\seeder\services\fields;

use craft\base\ElementInterface;
use craft\base\FieldInterface;

class VideoInput extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(FieldInterface $field, ElementInterface $element = null)
    {
        //        $type = $this->factory->randomElement(['youtube', 'vimeo']);
        $type = 'youtube';

        $videos = [
            'youtube' => [
                // stocks from playlist
                // https://www.youtube.com/watch?v=Z_M4o74OeQU&list=PLw1GDxO7G8onYy8ZgVx_QughDa9VGfjMn&index=5
                "GpcaJQ40q1Y",
                "BxF43wve21I",
                "rXuasgbBetc",
                "SDno1HI0pAE",
                "Z_M4o74OeQU",
                "I1M1D1KRZaw",
                "BOa0zQBRs_M",
                "KWnil5oy-Ic",
                "tZa_qQjsBoA",
                "RTLwaQFtXbE",
                "oem5-_YaY1E",
                "h4atYAGFYaY",
                "h5Cf7R0oP1g",
                "ZK9wtYOcChQ",
                "DW2iwEshWME",
                "xhmFeM1XQfM",
                "7HaJArMDKgI",
                "o0-KPoFoFm4",
                "lRTtMcx6rSM",
                "OOVCVNo29rQ",
                "OHz0xIR8uwI",
                "3QfDDKkxXUc",
                "o5NCbE6_kVY",
                "WQmGwmc-XUY",
                "JP35o3Ckk2A",
                "xo0JcWmtKN8",
                "J-FkR8L2X5E",
                "HqAw_jGoEkI",
                "CjBoK8f47EI",
                "auDetQ65HKA",
                "gHecJuvYcmU",
                "cEmaN79zGvE",
                "ng8Wivt52K0",
                "Jsl1g6puVSw",
                "gkkgDzumx2w",
                "SkUPvO_tMu0",
                "8LvAbf3B2y0",
                "3mUOyPhO0ww",
                "uqBOPUTazD4",
                "FsdQX3ASlg8",
                "eDaXTXnVsKc",
                "gQ0iwtYaC4k",
                "fX1anTQ3Hno",
                "Y62EgHvwa8k",
                "lY0VKtc40ZE",
                "dv_BeLpfY4Y",
                "7tOWXnNwR2w",
                "vScJLw_a5RM",
                "F_B0yx-4I2Y",
                "J_iTXrMGmO0",
                "7NyEX8DjOTw",
                "LcF6ut-1M94",
                "ooYnG_B7L00",
                "LN6EuNqxOwE",
                "yTN41PxbS7k",
                "N27Y1T7niHI",
                "hg11jQCMAUE",
                "D1Fw86xdU6g",
                "uaRny5FtJKY",
                "4eAzk2Y-P2A",
                "eKjXMP9Byjk",
                "RjicstoV8II",
                "w-yTVVmIvbE",
                "0akrHVVh3sg",
                "kxY7KkW_CR0",
                "0LXDf85zXOw",
                "ZBqvvq-ObE8",
                "5R-nEtNjydY",
                "2M0Fd4tyMr0",
                "qFjPSn4CIDY",
                "-FsqhrVxc7c",
                "TrE05CJgSLU",
                "dTqibxgh6d0",
                "_uz5hS5cJW0",
                "bgyqOlii_Cc",
                "ax1HkfBQJ8M",
                "oP2Ad-PZmuY",
                "NZmOg_VwWss",
                "07KjbkHpfc4",
                "ofsvxTZSUS8",
                "wUIQwd-EgEA",
                "Y8w-2lzM-C4",
                "tBZbSiVCBCY",
                "BpSygUv0qEE",
                "F7Yso1Oxu4k",
                "A-G-Z2-QUJ0",
                "gx1F2Hvnk18",
                "eEY50BOF0wM",
                "Ij-8Ilis4Ic",
                "KXkkKm4AwBg",
                "ls0BZBjk0Dc",
                "WsrPY5tmDXs",
                "ZdXao5XqeqM",
                "QWd9jMvJfuM",
                "ld5ViCyUSaY",
                "9wVrNhSwXnc",
                "wDS818n8LjU",
                "Zk-1J43U9cA",
                "IH5J-uC4vyY",
                "aw8wXpeTorc",
                "IvhdgXRydCA",
                "qSQndpPprX8"
            ],
            'vimeo'   => [

            ]
        ];

        return [
            'type'    => $type,
            'id'      => $this->factory->randomElement($videos[$type]),
            'startAt' => '',
        ];
    }
}
