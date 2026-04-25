<?php
/**
 * @package     Hoochicken\Module\Qlmenu
 *
 * @copyright   Copyright (C) 2026 Mareike Riegel. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

namespace Hoochicken\Module\Qlmenu\Site\Dispatcher;

defined('_JEXEC') or die;

use Exception;
use Hoochicken\Module\Qlmenu\Site\Helper\ParametersCustom;
use Hoochicken\Module\Qlmenu\Site\Helper\QlmenuHelper;
use Hoochicken\Module\Qlmenu\Site\Helper\SlideCollection;
use Hoochicken\Module\Qlmenu\Site\Helper\SlideItem;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry;

class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    const SLIDER_NUMBER_TOTAL = 10;

    private ?Registry $params = null;


    use HelperFactoryAwareTrait;

    /**
     * Runs the dispatcher.
     *
     * @return  void
     *
     * @since   5.4.0
     */
    public function dispatch()
    {
        $displayData = $this->getLayoutData();

        if (!$displayData['list']) {
            return;
        }

        parent::dispatch();
    }

    /**
     * Returns the layout data.
     *
     * @return  array
     *
     * @since   5.4.0
     */
    protected function getLayoutData()
    {
        $data = parent::getLayoutData();

        $menuHelper = $this->getHelperFactory()->getHelper('QlmenuHelper');

        $data['list']       = $menuHelper->getItems($data['params'], $data['app']);
        $data['base']       = $menuHelper->getBaseItem($data['params'], $data['app']);
        $data['active']     = $menuHelper->getActiveItem($data['app']);
        $data['default']    = $menuHelper->getDefaultItem($data['app']);
        $data['active_id']  = $data['active']->id;
        $data['default_id'] = $data['default']->id;
        $data['path']       = $data['base']->tree;
        $data['showAll']    = $data['params']->get('showAllChildren', 1);
        $data['class_sfx']  = htmlspecialchars($data['params']->get('class_sfx', ''), ENT_COMPAT, 'UTF-8');

        return $data;
    }

    public function Xdispatch()
    {
        try {
            $this->loadLanguage();

            $displayData = $this->getLayoutData();
            if ($this->isProperDisplayCustom($displayData)) {
                return;
            }

            /** @var ParametersCustom $displayData */
            $displayData = $displayData['data'] ?? null;
            require ModuleHelper::getLayoutPath('mod_qlmenu', $displayData->getLayout());
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    protected function isProperDisplayCustom(array $displayData): bool
    {
        return empty($displayData) || !isset($displayData['data']) || ParametersCustom::class !== get_class($displayData['data']);
    }

    /**
     * this method has a feeble array as return value due to given framework
     * anyway, works :-)
     * @return array|false|string
     */
    protected function XgetLayoutData()
    {
        try {
            $data = parent::getLayoutData();
            $this->params = new Registry($data['params']);

            /** @var QlmenuHelper $helper */
            $helper = $this->getHelperFactory()->getHelper(QlmenuHelper::class);

            $displayModel = new ParametersCustom($this->params ?? null, $this->module);
            // $displayModel->setMessage($helper->getMessage($this->params, $this->getApplication()));

            $displayModel->setSlides($this->getSlideCollection($this->params));
            return $displayModel->toArray();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
