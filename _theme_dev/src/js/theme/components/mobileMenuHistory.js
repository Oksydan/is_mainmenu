const menuHistory = [];

/**
 * A utility for managing the history of mobile menu interactions.
 * @namespace
 * @typedef {Object} MobileMenuHistory
 * @property {function(string): void} add - Adds a menu item ID to the history.
 * @property {function(): string} removeLast - Retrieves and removes the last added menu item ID from the history.
 * @property {function(): void} clear - Clears the entire menu history.
 */

/**
 * Creates a new instance of MobileMenuHistory.
 * @function
 * @returns {MobileMenuHistory} A new MobileMenuHistory instance.
 */
const mobileMenuHistory = () => {
  /**
   * Adds a menu item ID to the history.
   * @function
   * @memberof MobileMenuHistory
   * @param {string} id - The ID of the menu item to be added.
   * @return {void}
   */
  const add = (id) => {
    menuHistory.push(id);
  };

  /**
   * Retrieves and removes the last added menu item ID from the history.
   * @function
   * @memberof MobileMenuHistory
   * @throws {Error} Throws an error if the history is empty.
   * @return {string} The last added menu item ID.
   */
  const removeLast = () => {
    if (menuHistory.length === 0) {
      throw new Error('Cannot remove from an empty menu history.');
    }
    return menuHistory.pop();
  };

  /**
   * Clears the entire menu history.
   * @function
   * @memberof MobileMenuHistory
   * @return {void}
   */
  const clear = () => {
    menuHistory.length = 0;
  };

  return {
    add,
    removeLast,
    clear,
  };
};

export default mobileMenuHistory;
