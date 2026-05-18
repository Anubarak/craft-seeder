<?php

namespace Anubarak\Seeder\Listeners;

use CraftCms\Cms\Config\GeneralConfig;
use CraftCms\Cms\Element\Events\ElementMetaFieldsHtmlResolving;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Field\Matrix;
use CraftCms\Cms\Support\Html;
use Illuminate\Auth\AuthManager;

/**
 * MetaFieldHtml
 *
 * @author    Robin Schambach
 * @package   Anubarak\Seeder\Listeners
 * @since     18.05.26
 */
class MetaFieldHtml
{
    public function __construct(
        private readonly GeneralConfig $config,
        private readonly AuthManager   $auth
    ) {
    }

    public function handle(ElementMetaFieldsHtmlResolving $event)
    {
        if (!$this->config->devMode) {
            return;
        }

        if (!$this->auth->user()?->isAdmin()) {
            return;
        }
        if (!$event->element->id) {
            return;
        }

        if (!$event->element instanceof Entry) {
            return;
        }

        /** @var \CraftCms\Cms\Element\Contracts\ElementInterface $element */
        $element = $event->element;
        $show = false;
        $customFields = $element->getFieldLayout()?->getCustomFields() ?? [];
        foreach ($customFields as $field) {
            if ($field instanceof Matrix) {
                $show = true;
                break;
            }
        }


        //        Craft::$app->getView()->registerAssetBundle(SeederAssetBundle::class);


        $div = Html::tag('div', '', [
            'data-icon' => 'wand-magic-sparkles'
        ]);
        $content = Html::tag('button', $div . 'Seed Content', [
            'type'  => 'button',
            'data'  => [
                'element-id' => $event->element->id
            ],
            'class' => [
                'btn',
                'seed-element-content',
            ]
        ]);

        if ($show) {
            $div = Html::tag('div', '', [
                'data-icon' => 'wand-magic-sparkles'
            ]);
            $content .= Html::tag('button', $div . 'Seed Matrix', [
                'type' => 'button',

                'data'  => [
                    'element-id' => $event->element->id
                ],
                'class' => [
                    'btn',
                    'seed-element',
                ]
            ]);
        }

        $outerDiv = Html::tag('div', $content, [
            'class' => [
                'flex'
            ]
        ]);
        $event->html .= $outerDiv;
    }
}