/**
 * Creates a hover intent tracker for a given element.
 *
 * @param {HTMLElement} el - The element to track hover intent on.
 * @param {Function} onOver - The callback function to be called when the hover intent is detected.
 * @param {Function} onOut - The callback function to be called when the hover intent is not detected.
 * @param {HoverIntentOptions} [config] - Optional configuration for hover intent options.
 * @param {Function} onStartOver - The callback function to be called when the hover intent starts.
 * @param {Function} onStartOut - The callback function to be called when the out intent starts.
 * @returns {Object} - The hover intent tracker object with public methods.
 */
const useHoverIntent = (
  el,
  onOver,
  onOut,
  config = {},
  onStartOver = () => {},
  onStartOut = () => {},
) => {
  let x; let y; let pX; let
    pY;
  let mouseOver = false;
  let focused = false;
  let state = 0;
  let timer = 0;

  /**
   * Options for the hover intent tracker.
   *
   * @typedef {Object} HoverIntentOptions
   * @property {number} sensitivity - The sensitivity of the hover intent detection.
   * @property {number} interval - The interval for comparing mouse positions.
   * @property {number} timeout - The timeout for delaying the onOut callback.
   * @property {boolean} handleFocus - Whether to handle focus events or not.
   */

  /** @type {HoverIntentOptions} */
  const options = {
    sensitivity: 7,
    interval: 200,
    timeout: 100,
    handleFocus: false,
    ...config,
  };

  /**
   * Delays the onOut callback and clears the timer.
   *
   * @param {HTMLElement} element - The element triggering the hover intent.
   * @param {MouseEvent} e - The mouse event object.
   * @returns {undefined} - Returns undefined if focused, otherwise calls onOut callback.
   */
  function delay(element, e) {
    if (timer) {
      clearTimeout(timer);
    }

    state = 0;
    return focused ? undefined : onOut.call(element, e, element);
  }

  /**
   * Updates the mouse position.
   *
   * @param {MouseEvent} e - The mouse event object.
   */
  function tracker(e) {
    x = e.clientX;
    y = e.clientY;
  }

  /**
   * Compares the current mouse position with the previous position.
   *
   * @param {HTMLElement} element - The element triggering the hover intent.
   * @param {MouseEvent} e - The mouse event object.
   * @returns {undefined} - Returns undefined if focused, otherwise calls onOver callback.
   */
  function compare(element, e) {
    if (timer) {
      clearTimeout(timer);
    }

    if (Math.abs(pX - x) + Math.abs(pY - y) < options.sensitivity) {
      state = 1;
      return focused ? undefined : onOver.call(element, e, element);
    }

    pX = x;
    pY = y;
    timer = setTimeout(() => {
      compare(element, e);
    }, options.interval);

    return undefined;
  }

  /**
   * Handles the focus event and triggers the onOver callback if not already in a hover state.
   *
   * @param {FocusEvent} e - The focus event object.
   */
  function handleFocus(e) {
    if (!mouseOver) {
      focused = true;
      onOver.call(el, e);
    }
  }

  /**
   * Handles the blur event and triggers the onOut callback if not in a hover state and focused.
   *
   * @param {FocusEvent} e - The blur event object.
   */
  function handleBlur(e) {
    if (!mouseOver && focused) {
      focused = false;
      onOut.call(el, e);
    }
  }

  /**
   * Adds focus event listeners.
   */
  function addFocus() {
    el.addEventListener('focus', handleFocus, false);
    el.addEventListener('blur', handleBlur, false);
  }

  /**
   * Removes focus event listeners.
   */
  function removeFocus() {
    el.removeEventListener('focus', handleFocus, false);
    el.removeEventListener('blur', handleBlur, false);
  }

  // Public methods

  /**
   * Sets or updates options for the hover intent tracker.
   *
   * @param {HoverIntentOptions} opt - The options to be updated.
   * @returns {Object} - The hover intent tracker object for chaining.
   */
  function setOptions(opt) {
    const focusOptionChanged = opt.handleFocus !== options.handleFocus;
    options.sensitivity = opt.sensitivity || options.sensitivity;
    options.interval = opt.interval || options.interval;
    options.timeout = opt.timeout || options.timeout;
    options.handleFocus = opt.handleFocus || options.handleFocus;

    if (focusOptionChanged) {
      if (options.handleFocus) {
        addFocus();
      } else {
        removeFocus();
      }
    }

    return this;
  }

  /**
   * Handles the mouseover event and starts the hover intent tracking.
   *
   * @param {MouseEvent} e - The mouse event object.
   * @returns {Object} - The hover intent tracker object for chaining.
   */
  function handleMouseOver(e) {
    mouseOver = true;
    if (timer) {
      clearTimeout(timer);
    }

    onStartOver.call(el, e, el);

    el.removeEventListener('mousemove', tracker, false);

    if (state !== 1) {
      pX = e.clientX;
      pY = e.clientY;

      el.addEventListener('mousemove', tracker, false);

      timer = setTimeout(() => {
        compare(el, e);
      }, options.interval);
    }

    return this;
  }

  /**
   * Handles the mouseout event and stops the hover intent tracking.
   *
   * @param {MouseEvent} e - The mouse event object.
   * @returns {Object} - The hover intent tracker object for chaining.
   */
  function handleMouseOut(e) {
    mouseOver = false;
    if (timer) {
      clearTimeout(timer);
    }

    onStartOut.call(el, e, el);

    el.removeEventListener('mousemove', tracker, false);

    if (state === 1) {
      timer = setTimeout(() => {
        delay(el, e);
      }, options.timeout);
    }

    return this;
  }

  /**
   * Removes all event listeners and cleans up the hover intent tracker.
   */
  function remove() {
    if (!el) return;
    el.removeEventListener('mouseover', handleMouseOver, false);
    el.removeEventListener('mouseout', handleMouseOut, false);
    removeFocus();
  }

  // Event listeners
  if (el) {
    el.addEventListener('mouseover', handleMouseOver, false);
    el.addEventListener('mouseout', handleMouseOut, false);
  }

  // Public methods
  return {
    setOptions,
    remove,
  };
};

export default useHoverIntent;
