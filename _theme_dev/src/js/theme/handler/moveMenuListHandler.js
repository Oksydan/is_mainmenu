import fetchSubmenuHandler from './fetchSubmenuHandler';
import mobileMenuHistory from '../components/mobileMenuHistory';
import menuTrackHandler from './menuTrackHandler';

/**
 * Handles the movement and interactions of a mobile menu list.
 * @typedef {Object} MoveMenuListHandler
 * @property {function(Number): void} goToMenuList - Navigates to the specified menu list by ID.
 * @property {function(): void} back - Navigates back to the previous menu list.
 * @property {function(): void} reset - Resets the menu to its initial state.
 * @property {function(HTMLElement): Promise<void>} fetchSubMenuAndRender - Fetches and renders submenus for a given menu list.
 */

/**
 * Creates a new instance of MoveMenuListHandler.
 * @function
 * @param {Object} options - Options for configuring the MoveMenuListHandler.
 * @param {string} options.mobileMenuTrack - Selector for the mobile menu track element.
 * @param {string} options.menuListElementSelector - Selector for menu list elements.
 * @param {string} options.mobileMenuMainWrapper - Selector for the main wrapper of the mobile menu.
 * @param {string} options.nextLevelBtnSelector - Selector for next-level buttons in the menu.
 * @param {string} options.menuListActiveClass - CSS class for marking an active menu list.
 * @returns {MoveMenuListHandler} A new MoveMenuListHandler instance.
 */
const moveMenuListHandler = ({
                               mobileMenuTrack,
                               menuListElementSelector,
                               mobileMenuMainWrapper,
                               nextLevelBtnSelector,
                               menuListActiveClass,
                             }) => {
  const loadedMenuSet = new Set();
  const {
    add: addToHistory,
    removeLast: removeLastFromHistory,
    clear: clearHistory,
  } = mobileMenuHistory();
  let _trackHandler = null;

  /**
   * Gets the menu track handler.
   * @function
   * @inner
   * @return {MenuTrackHandler} The menu track handler instance.
   */
  const getTrackHandler = () => {
    if (!_trackHandler) {
      _trackHandler = menuTrackHandler(document.querySelector(mobileMenuTrack));
    }

    return _trackHandler;
  };

  /**
   * Sets or removes the loading class from the main wrapper.
   * @function
   * @inner
   * @param {boolean} isLoading - Whether to set or remove the loading class.
   * @return {void}
   */
  const setLoading = (isLoading) => {
    const mainWrapper = document.querySelector(mobileMenuMainWrapper);

    if (!mainWrapper) {
      return;
    }

    if (isLoading) {
      mainWrapper.classList.add('loading');
    } else {
      mainWrapper.classList.remove('loading');
    }
  };

  /**
   * Gets the IDs of elements from the given menu list.
   * @function
   * @inner
   * @param {HTMLElement} list - The menu list element.
   * @return {Array<string>} An array of IDs.
   */
  const getIdsElementsFromList = (list) => {
    const ids = [];

    list.querySelectorAll(nextLevelBtnSelector).forEach((element) => {
      ids.push(element.getAttribute('data-id'));
    });

    return ids;
  };

  /**
   * Fetches submenus and renders them for the given menu list.
   * @function
   * @inner
   * @param {HTMLElement} list - The menu list element.
   * @return {Promise<void>} A Promise that resolves when the submenus are fetched and rendered.
   */
  const fetchSubMenuAndRender = async (list) => {
    const ids = getIdsElementsFromList(list);
    const idsToFetch = [];

    for (const id of ids) {
      if (!loadedMenuSet.has(id)) {
        idsToFetch.push(id);
        loadedMenuSet.add(id);
      }
    }

    if (idsToFetch.length === 0) {
      return;
    }

    setLoading(true);
    const { html, success } = await fetchSubmenuHandler(idsToFetch);

    if (success) {
      const elements = new DOMParser().parseFromString(html, 'text/html').body.childNodes;

      elements.forEach((element) => {
        document.querySelector(mobileMenuTrack)?.append(element);
      });
    }

    setLoading(false);
  };

  /**
   * Gets the calculated depth for transformation.
   * @function
   * @inner
   * @param {Number} depth - The depth of the menu track.
   * @return {number} The calculated depth for transformation.
   */
  const getDepthToCalc = (depth) => (depth > 0 ? depth - 1 : depth);

  /**
   * Navigates to the specified menu list by ID.
   * @function
   * @memberof MoveMenuListHandler
   * @param {Number} id - The ID of the menu list to navigate to.
   * @return {void}
   */
  const goToMenuList = (id) => {
    const { moveTrackToDepth } = getTrackHandler();
    const list = document.querySelector(`${menuListElementSelector}[data-id="${id}"]`);

    if (!list) {
      return;
    }

    const depth = list.getAttribute('data-depth');

    moveTrackToDepth(depth);

    list.classList.add(menuListActiveClass);
    list.style.left = `${getDepthToCalc(depth) * 100}%`;
    addToHistory(id);

    fetchSubMenuAndRender(list);
  };

  /**
   * Navigates back to the previous menu list.
   * @function
   * @memberof MoveMenuListHandler
   * @return {void}
   */
  const back = () => {
    const { moveTrackToDepth } = getTrackHandler();

    const lastId = removeLastFromHistory();
    const list = document.querySelector(`${menuListElementSelector}[data-id="${lastId}"]`);
    const depth = parseInt(list.getAttribute('data-depth'), 10);

    if (depth && !Number.isNaN(depth)) {
      moveTrackToDepth(depth - 1);
    }

    if (!list) {
      return;
    }

    list.classList.remove(menuListActiveClass);
  };

  /**
   * Resets the menu to its initial state.
   * @function
   * @memberof MoveMenuListHandler
   * @return {void}
   */
  const reset = () => {
    const { moveTrackToDepth } = getTrackHandler();
    const activeElements = document.querySelectorAll(`${menuListElementSelector}.${menuListActiveClass}`);

    activeElements.forEach((element) => {
      element.classList.remove(menuListActiveClass);
    });

    moveTrackToDepth(0);
    goToMenuList(0); // 0 is the ID of the main menu
    clearHistory();
  };

  return {
    goToMenuList,
    back,
    reset,
    fetchSubMenuAndRender,
  };
};

export default moveMenuListHandler;
