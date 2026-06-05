<?php

namespace Anubarak\Seeder\Element\Actions;


use CraftCms\Cms\Element\Actions\ElementAction;
use CraftCms\Cms\Support\Facades\HtmlStack;
use function CraftCms\Cms\t;

/**
 * Craft CMS Plugins
 *
 * Created with PhpStorm.
 *
 * @link      https://github.com/Anubarak/
 * @email     anubarak1993@gmail.com
 * @copyright Copyright (c) 2024 Robin Schambach|Secondred Newmedia GmbH
 *
 * @property-read null|string $triggerHtml
 * @property-read string      $triggerLabel
 */
class PopulateAction extends ElementAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return t('Populate Element(s)', category: 'element-seeder');
    }

    /**
     * @inheritdoc
     */
    public function getTriggerHtml(): ?string
    {
        HtmlStack::jsWithVars(function($actionClass) {
            return <<<JS
(() => {
  new Craft.ElementActionTrigger({
    type: $actionClass,
    bulk: true,
    requireId: false,
    activate: (selectedItems, elementIndex) => {
      const selectedIds = selectedItems.toArray().map((item) => {
        return parseInt($(item).data('id'));
      });
       const slideOut = new Craft.CpScreenSlideout('element-seeder/element-content-modal', {
        showHeader: true,
        params: {
            elementIds: selectedIds
        }
    });
       
       slideOut.on('submit', () => {
            elementIndex.updateElements(true);
       })
      
    },
  });
})();
JS;
        }, [
            static::class,
        ]);

        return null;
    }
}