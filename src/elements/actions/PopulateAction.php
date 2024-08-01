<?php

namespace anubarak\seeder\elements\actions;

use craft\base\ElementAction;

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
        return \Craft::t('element-seeder', 'Populate Element(s)');
    }

    /**
     * @inheritdoc
     */
    public function getTriggerHtml(): ?string
    {
        \Craft::$app->getView()->registerJsWithVars(function($actionClass) {
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
       const slideOut = new Craft.CpScreenSlideout('element-seeder/seeder/element-content-modal', {
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