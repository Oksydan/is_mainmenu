import fetchSubmenuHandler from "./fetchSubmenuHandler";
import mobileMenuHistory from "../components/mobileMenuHistory";
import menuTrackHandler from "./menuTrackHandler";

/**
 * @param mobileMenuTrack
 * @param menuListElementSelector
 * @param mobileMenuMainWrapper
 * @param nextLevelBtnSelector
 * @param menuListActiveClass
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
    clear: clearHistory
  } = mobileMenuHistory();
  let _trackHandler = null;

  /**
   * Get track handler
   * @return {menuTrackHandler} _trackHandler
   */
  const getTrackHandler = () => {
    if (!_trackHandler) {
      _trackHandler = menuTrackHandler(document.querySelector(mobileMenuTrack));
    }

    return _trackHandler;
  }

  /**
   * Set loading class to main wrapper
   * @param {boolean} isLoading
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
  }

  /**
   * Get ids from list
   * @param {HTMLElement} list
   * @return {*[int]} ids
   */
  const getIdsElementsFromList = (list) => {
    const ids = [];

    list.querySelectorAll(nextLevelBtnSelector).forEach((element) => {
      ids.push(element.getAttribute('data-id'));
    });

    return ids;
  }

  /**
   * Fetch submenu and render it for given list
   * @param {HTMLElement} list
   * @return {Promise<void>}
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
  }

  /**
   * Get calculated depth
   * @param {Number} depth
   * @return {number|*}
   */
  const getDepthToCalc = (depth) => {
    return depth > 0 ? depth - 1 : depth;
  }

  /**
   * Go to menu list
   * @param {Number} id
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
  }

  /**
   * Back to previous menu list
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
  }

  /**
   * Reset menu to initial state
   * @return {void}
   */
  const reset = () => {
    const { moveTrackToDepth } = getTrackHandler();
    const activeElements = document.querySelectorAll(`${menuListElementSelector}.${menuListActiveClass}`);

    activeElements.forEach((element) => {
      element.classList.remove(menuListActiveClass);
    });

    moveTrackToDepth(0);
    goToMenuList(0); // 0 is id of main menu
    clearHistory();
  }

  return {
    goToMenuList,
    back,
    reset,
    fetchSubMenuAndRender,
  }
}

export default moveMenuListHandler;
