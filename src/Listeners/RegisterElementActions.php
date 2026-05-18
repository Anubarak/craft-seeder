<?php

namespace Anubarak\Seeder\Listeners;


use Anubarak\Seeder\Element\Actions\NumerizeAction;
use Anubarak\Seeder\Element\Actions\PopulateAction;
use CraftCms\Cms\Config\GeneralConfig;
use Illuminate\Auth\AuthManager;

/**
 * RegisterElementActions
 *
 * @author    Robin Schambach
 * @package   Anubarak\Seeder\Listeners
 * @since     18.05.26
 */
class RegisterElementActions
{
    public function __construct(
        private readonly GeneralConfig $config,
        private readonly AuthManager $auth
    )
    {
    }

    public function handle(\CraftCms\Cms\Element\Events\ElementActionsResolving $event)
    {
        if (!$this->config->devMode) {
            return;
        }

        if (!$this->auth->user()?->isAdmin()) {
            return;
        }
        $event->actions[] = PopulateAction::class;
        $event->actions[] = NumerizeAction::class;
    }
}