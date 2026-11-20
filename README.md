## Qlmenu Joomla Module

Qlmenu (`mod_qlmenu`) is a Joomla 6 site module that renders a menu as a horizontal dropdown navigation with up to three styled levels and a mobile toggle.

### Features

- Any site menu as source, with **base item**, **start/end level** and **show sub-menu items** options (same semantics as core `mod_menu`)
- Per level (1st / 2nd / 3rd): text colour, background, hover and active colours, padding, width, font size, and border (levels 2 and 3)
- Styles are scoped to each module instance (`ul#mod-qlmenu-{moduleId}`), so several qlmenu modules can live on one page
- Keyboard-accessible sub-menu toggles (`aria-expanded`), mobile toggle button
- Two layouts: `default` and `collapse-default` (Bootstrap navbar collapse)
- Template overrides work as usual: `templates/<template>/html/mod_qlmenu/`

### Architecture (Joomla 4/5/6 module structure)

```
mod_qlmenu.xml                  manifest, declares namespace Hoochicken\Module\Qlmenu (src/)
services/provider.php           DI service provider: ModuleDispatcherFactory, HelperFactory, Module
src/Dispatcher/Dispatcher.php   builds the layout data; extends AbstractModuleDispatcher
src/Helper/QlmenuHelper.php     loads/filters menu items (cached), computes <li> classes, builds inline CSS
src/Helper/Parameters*.php      typed access to module params (ParametersCustom = qlmenu specific params)
src/Helper/Display*.php         view model handed to the layout ($displayModel)
src/Helper/Error*.php           error collection used by the view model
tmpl/default.php                main layout; sub-layouts default_{component,url,heading,separator}.php
tmpl/collapse-default.php       Bootstrap collapse wrapper around default.php
media/joomla.asset.json         web assets mod_qlmenu.script / mod_qlmenu.style
_jupdate/mod_qlmenu.xml         update server file
```

Request flow:

1. Joomla boots the module through `services/provider.php`; classes in `src/` are autoloaded via the manifest `<namespace>`.
2. `Dispatcher::getLayoutData()` gets `QlmenuHelper` from the helper factory, loads the menu items, and wraps params and items in `ParametersCustom` / `DisplayCustom`.
3. `AbstractModuleDispatcher::dispatch()` extracts the layout data (`$list`, `$params`, `$module`, `$app`, `$displayModel`, `$menuId`, `$class_sfx`, …) and includes the selected layout.
4. The layout registers the module's asset file with the WebAssetManager, uses script and style, and adds the generated inline CSS.

### Configuration

1. Install the module ZIP via **System → Install → Extensions**.
2. Go to **Content → Site Modules → New → Qlmenu**.
3. **Module** tab: choose the menu, base item, start/end level.
4. **Styles Level 1/2/3** tabs: adjust colours, padding, widths, borders and font sizes.
5. **Advanced** tab: layout, menu class, module class, caching.
6. Assign a position and the pages, then save.
