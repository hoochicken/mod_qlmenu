## Qlmenu Joomla Module

Qlmenu is a Joomla 5/6 site module that displays a configurable hero slider with up to **10 slides**. Each slide can have its own image, title, and rich-text content, and can be individually enabled or disabled. The module also provides flexible slider behavior settings such as autoplay, caption alignment, and navigation visibility.

### Features

- **Up to 10 slides** with:
  - Image
  - Title
  - Text (editor / HTML)
  - Per-slide display toggle
- **Slider behavior settings**:
  - `autoplayMs`: interval between slides (milliseconds, 0 = no autoplay)
  - `boxAlign`: caption box alignment (`left` or `right`)
  - `displayNavigationPrevNext`: show/hide previous/next buttons
  - `displayNavigationDots`: show/hide pagination dots
- **Responsive layout** and keyboard / touch navigation

### How it works (architecture)

- **Models**
  - `ParametersBasic` / `ParametersBasicInterface`: base data for the module (params, module, message, errors).
  - `DisplayCustom` / `DisplayCustomInterface`: extends the basic display with slide-related data and slider settings.
  - `SlideItem`: represents a single slide (display flag, image, title, text, helper methods like `existsImage()`, `existsTitle()`, `existsText()`).
  - `SlideCollection`: collection of `SlideItem` objects with `add()`, `set()`, `get()`, `isEmpty()`.
  - `ErrorItem`, `ErrorCollection`: encapsulate error handling.

- **Dispatcher**
  - `src/Dispatcher/Dispatcher.php` builds the `DisplayCustom` model from module params:
    - Reads params for slides 1–10.
    - Creates `SlideItem` instances for slides with configured data.
    - Adds them to a `SlideCollection` and injects it into `DisplayCustom`.
    - Populates error collections if needed.

- **Template**
  - `tmpl/default.php`:
    - Loads assets via Joomla WebAsset Manager (`mod_qlmenu.script`, `mod_qlmenu.style`).
    - Uses `DisplayCustom` to:
      - Render the module wrapper, title, and message.
      - Loop through the `SlideCollection` and render `<section class="slide">` for each slide where `isDisplay()` is `true` and the image exists.
      - Only output title and text when `existsTitle()` / `existsText()` are `true`.
    - Passes slider settings to JavaScript using `Document::addScriptOptions('mod_qlmenu.config', [...])` and the wrapper methods:
      - `getAutoplayMs()`
      - `getBoxAlign()`
      - `displayNavigationPrevNext()`
      - `displayNavigationDots()`

- **JavaScript**
  - `media/js/script.js`:
    - Reads options from `Joomla.getOptions('mod_qlmenu.config')` and exposes them as `window.SLIDER_CONFIG`:
      - `autoplayMs`
      - `boxAlign`
      - `displayNavigationPrevNext`
      - `displayNavigationDots`
    - Merges them with internal `DEFAULT_CONFIG` and initializes the slider on `#heroSlider`.
    - Handles:
      - Autoplay (start/pause, configurable interval)
      - Previous/next buttons
      - Dots and active state
      - Keyboard navigation (left/right, space for play/pause)
      - Basic touch swipe support

### Configuration in Joomla

1. Install or copy the module into your Joomla instance so that it is available as `mod_qlmenu`.
2. In the Joomla Administrator, go to **Extensions → Modules** and create or edit a module of type **Qlmenu**.
3. In the **Slides** fieldset:
   - For each slide (1–10):
     - Enable **Display** to show the slide.
     - Select an **Image**.
     - Enter a **Title** (optional).
     - Enter **Text** (optional, supports HTML via editor).
4. In the **Settings** fieldset:
   - **Autoplay (ms)**: set the interval, e.g. `3000` for 3 seconds, or `0` to disable autoplay.
   - **Caption alignment**: choose **Left** or **Right**.
   - **Show previous/next buttons**: toggle arrow navigation.
   - **Show navigation dots**: toggle pagination dots below the slider.
5. Assign the module to a position and menu items as usual, then save and publish.

### Frontend behavior

- Only slides with **Display = Yes** and a valid image path are rendered.
- Titles and texts are optional; if omitted, their corresponding elements are not rendered.
- Slider will respect the configured autoplay interval and navigation visibility.

### Development notes

- Settings are stored as module params in `mod_qlmenu.xml`.
- They are read in `DisplayCustom` and exposed via simple getter methods.
- The template passes them to JS with `addScriptOptions`, and the JS reads them using `Joomla.getOptions` to configure the slider.

This README is intended as an overview for both users configuring the module in Joomla and developers maintaining or extending its code. 