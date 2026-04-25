<?php
/**
 * mod_qlmenu
 *
 * @copyright  Copyright (C) 2026. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Hoochicken\Module\Qlmenu\Site\Helper;

use Joomla\Registry\Registry;
use stdClass;

require_once __DIR__ . '/ParametersBasicInterface.php';

/**
 * this class contains all basic functionality that a module needs
 * specifications for the custom module will be put into ParametersCustom
 */
class ParametersBasic implements ParametersBasicInterface
{
    protected ?Registry $params = null;
    protected ?stdClass $module = null;
    protected ?string $message = null;
    protected ?MessageCollection $errors = null;

    public function __construct(Registry $params, stdClass $module)
    {
        $this->params = $params;
        $this->module = $module;
    }

    /**
     * @return array{ message: string, params: Registry, module: stdClass, errors: MessageItem[] }
     */
    public function toArray(): array
    {
        return [
            'data' => $this,
            'message' => $this->message,
            'params' => $this->params,
            'module' => $this->module,
            'errors' => $this->errors,
        ];
    }

    public function displayTitle(): bool
    {
        return (bool)$this->module?->showtitle ?? false;
    }

    public function getTitle(): string
    {
        return (string)$this->module?->title;
    }

    public function getTitleTag(): string
    {
        return $this->params->get('header_tag', 'h3');
    }

    public function getModuleTag(): string
    {
        return (string)$this->params->get('module_tag', 'div');
    }

    public function getModuleClassSuffix(bool $specialChars = true): string
    {
        $moduleClassSuffix = (string)$this->params->get('moduleclass_sfx', '');
        if (!$specialChars) {
            return $moduleClassSuffix;
        }
        return htmlspecialchars($moduleClassSuffix, ENT_COMPAT, 'UTF-8');
    }

    public function getLayout(): string
    {
        return (string)$this->params->get('layout', '');
    }

    public function existsErrors(): bool
    {
        return !is_null($this->errors) && !$this->errors->hasErrors();
    }

    public function getParams(): ?Registry
    {
        return $this->params;
    }

    public function setParams(?Registry $params): void
    {
        $this->params = $params;
    }

    public function getModule(): ?stdClass
    {
        return $this->module;
    }

    public function setModule(?stdClass $module): void
    {
        $this->module = $module;
    }

    public function existsMessage(): bool
    {
        return !empty(trim(strip_tags($this->message ?? '')));
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getErrors(): ?MessageCollection
    {
        return $this->errors;
    }

    public function setErrors(?MessageCollection $errors): void
    {
        $this->errors = $errors;
    }
}
