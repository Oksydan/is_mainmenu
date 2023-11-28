/**
 * Creates a hover intent tracker for a given element.
 *
 * @param {HTMLElement} targetElement - The element to track hover intent on.
 * @param {Function} onHover - The callback function to be called when the hover intent is detected.
 * @param {Function} onOut - The callback function to be called when the hover intent is not detected.
 * @param {HoverIntentOptions} [config] - Optional configuration for hover intent options.
 * @param {Function} onStartHover - The callback function to be called when the hover intent starts.
 * @param {Function} onStartOut - The callback function to be called when the out intent starts.
 * @returns {Object} - The hover intent tracker object with public methods.
 */
const useHoverIntent = (
  targetElement,
  onHover,
  onOut,
  config = {},
  onStartHover = () => {},
  onStartOut = () => {},
) => {
  let x;
  let y;
  let pX;
  let pY;
  let isMouseOver = false;
  let isFocused = false;
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
   * @param {HTMLElement} triggeringElement - The element triggering the hover intent.
   * @param {MouseEvent} event - The mouse event object.
   * @returns {undefined} - Returns undefined if focused, otherwise calls onOut callback.
   */
  function delay(triggeringElement, event) {
    if (timer) {
      clearTimeout(timer);
    }

    state = 0;
    return isFocused ? undefined : onOut.call(triggeringElement, event, triggeringElement);
  }

  /**
   * Updates the mouse position.
   *
   * @param {MouseEvent} event - The mouse event object.
   */
  function updateMousePosition(event) {
    x = event.clientX;
    y = event.clientY;
  }

  /**
   * Compares the current mouse position with the previous position.
   *
   * @param {HTMLElement} triggeringElement - The element triggering the hover intent.
   * @param {MouseEvent} event - The mouse event object.
   * @returns {undefined} - Returns undefined if focused, otherwise calls onHover callback.
   */
  function compareMousePositions(triggeringElement, event) {
    if (timer) {
      clearTimeout(timer);
    }

    if (Math.abs(pX - x) + Math.abs(pY - y) < options.sensitivity) {
      state = 1;
      return isFocused ? undefined : onHover.call(triggeringElement, event, triggeringElement);
    }

    pX = x;
    pY = y;
    timer = setTimeout(() => {
      compareMousePositions(triggeringElement, event);
    }, options.interval);

    return undefined;
  }

  /**
   * Handles the focus event and triggers the onHover callback if not already in a hover state.
   *
   * @param {FocusEvent} event - The focus event object.
   */
  function handleFocus(event) {
    if (!isMouseOver) {
      isFocused = true;
      onHover.call(targetElement, event, targetElement);
    }
  }

  /**
   * Handles the blur event and triggers the onOut callback if not in a hover state and focused.
   *
   * @param {FocusEvent} event - The blur event object.
   */
  function handleBlur(event) {
    if (!isMouseOver && isFocused) {
      isFocused = false;
      onOut.call(targetElement, event, targetElement);
    }
  }

  /**
   * Adds focus event listeners.
   */
  function addFocusEventListeners() {
    targetElement.addEventListener('focus', handleFocus, false);
    targetElement.addEventListener('blur', handleBlur, false);
  }

  /**
   * Removes focus event listeners.
   */
  function removeFocusEventListeners() {
    targetElement.removeEventListener('focus', handleFocus, false);
    targetElement.removeEventListener('blur', handleBlur, false);
  }

  // Public methods

  /**
   * Sets or updates options for the hover intent tracker.
   *
   * @param {HoverIntentOptions} newOptions - The options to be updated.
   * @returns {Object} - The hover intent tracker object for chaining.
   */
  function setOptions(newOptions) {
    const focusOptionChanged = newOptions.handleFocus !== options.handleFocus;
    options.sensitivity = newOptions.sensitivity || options.sensitivity;
    options.interval = newOptions.interval || options.interval;
    options.timeout = newOptions.timeout || options.timeout;
    options.handleFocus = newOptions.handleFocus || options.handleFocus;

    if (focusOptionChanged) {
      if (options.handleFocus) {
        addFocusEventListeners();
      } else {
        removeFocusEventListeners();
      }
    }

    return this;
  }

  /**
   * Handles the mouseover event and starts the hover intent tracking.
   *
   * @param {MouseEvent} event - The mouse event object.
   * @returns {Object} - The hover intent tracker object for chaining.
   */
  function handleMouseOver(event) {
    isMouseOver = true;
    if (timer) {
      clearTimeout(timer);
    }

    onStartHover.call(targetElement, event, targetElement);

    targetElement.removeEventListener('mousemove', updateMousePosition, false);

    if (state !== 1) {
      pX = event.clientX;
      pY = event.clientY;

      targetElement.addEventListener('mousemove', updateMousePosition, false);

      timer = setTimeout(() => {
        compareMousePositions(targetElement, event);
      }, options.interval);
    }

    return this;
  }

  /**
   * Handles the mouseout event and stops the hover intent tracking.
   *
   * @param {MouseEvent} event - The mouse event object.
   * @returns {Object} - The hover intent tracker object for chaining.
   */
  function handleMouseOut(event) {
    isMouseOver = false;
    if (timer) {
      clearTimeout(timer);
    }

    onStartOut.call(targetElement, event, targetElement);

    targetElement.removeEventListener('mousemove', updateMousePosition, false);

    if (state === 1) {
      timer = setTimeout(() => {
        delay(targetElement, event);
      }, options.timeout);
    }

    return this;
  }

  /**
   * Removes all event listeners and cleans up the hover intent tracker.
   */
  function removeEventListeners() {
    if (!targetElement) return;
    targetElement.removeEventListener('mouseover', handleMouseOver, false);
    targetElement.removeEventListener('mouseout', handleMouseOut, false);
    removeFocusEventListeners();
  }

  // Event listeners
  if (targetElement) {
    targetElement.addEventListener('mouseover', handleMouseOver, false);
    targetElement.addEventListener('mouseout', handleMouseOut, false);
  }

  // Public methods
  return {
    setOptions,
    removeEventListeners,
  };
};

export default useHoverIntent;
