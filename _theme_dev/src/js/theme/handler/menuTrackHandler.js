/**
 * A utility for handling the movement of a menu track within an HTMLElement.
 * @typedef {Object} MenuTrackHandler
 * @property {function(Number): void} moveTrackToDepth - Moves the menu track to the specified depth.
 */

/**
 * Creates a new instance of MenuTrackHandler.
 * @function
 * @param {HTMLElement} trackElement - The HTML element representing the menu track.
 * @returns {MenuTrackHandler} A new MenuTrackHandler instance.
 */
const menuTrackHandler = (trackElement) => {
  /**
   * Gets the calculated depth for transformation.
   * @function
   * @inner
   * @param {Number} depth - The depth of the menu track.
   * @return {number} The calculated depth for transformation.
   */
  const getDepthToCalc = (depth) => (depth > 0 ? depth - 1 : depth);

  /**
   * Moves the menu track to the specified depth.
   * @function
   * @memberof MenuTrackHandler
   * @param {Number} depth - The depth to which the menu track should be moved.
   * @return {void}
   */
  const moveTrackToDepth = (depth) => {
    if (!trackElement) {
      return;
    }

    trackElement.style.transform = `translateX(-${getDepthToCalc(depth) * 100}%)`;
  };

  return {
    moveTrackToDepth,
  };
};

export default menuTrackHandler;
