const menuHistory = [];

/**
 * Mobile menu history handler
 */
const mobileMenuHistory = () => {
  /**
   * Add id to history
   * @param {string} id
   * @return {void}
   */
  const add = (id) => {
    menuHistory.push(id);
  };

  /**
   * Get last element from history and remove it
   * @return {string}
   */
  const removeLast = () => menuHistory.pop();

  /**
   * Clear history
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
