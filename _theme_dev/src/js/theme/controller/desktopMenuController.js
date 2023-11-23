import useHoverIntent from '../components/useHoverIntent';
import useDesktopMenu from '../components/useDesktopMenu';

/**
 * Controls the behavior of a desktop menu.
 * @typedef {Object} DesktopMenuController
 * @property {function(): void} init - Initializes the desktop menu controller, attaching necessary events.
 */

/**
 * Creates a new instance of DesktopMenuController.
 * @function
 * @param {Object} [params] - Additional parameters for configuring the desktop menu controller.
 * @param {string} [params.mainMenuListSelector='.js-main-menu-list'] - Selector for the main menu list.
 * @param {string} [params.mainMenuItemSelector='.js-main-menu-item'] - Selector for the main menu items.
 * @param {string} [params.mainMenuItemSubSelector='.js-main-menu-sub'] - Selector for the submenus within main menu items.
 * @returns {DesktopMenuController} A new DesktopMenuController instance.
 */
const desktopMenuController = (params = {}) => {
  const {
    mainMenuListSelector,
    mainMenuItemSelector,
    mainMenuItemSubSelector,
  } = {
    mainMenuListSelector: '.js-main-menu-list',
    mainMenuItemSelector: '.js-main-menu-item',
    mainMenuItemSubSelector: '.js-main-menu-sub',
    ...params,
  };

  const {
    handleMainMenuSubLinkMouseEvent,
    handleMainMenuSubLinkFetch,
  } = useDesktopMenu({
    mainMenuItemSubSelector,
  });

  /**
   * Attaches events to the main menu elements for handling hover and fetching submenus.
   * @function
   * @inner
   * @return {void}
   */
  const attachEvents = () => {
    const listElements = document.querySelectorAll(`${mainMenuListSelector} ${mainMenuItemSelector}`);

    listElements.forEach((listElement) => {
      useHoverIntent(
        listElement,
        handleMainMenuSubLinkMouseEvent,
        handleMainMenuSubLinkMouseEvent,
        {},
        handleMainMenuSubLinkFetch,
      );
    });
  };

  /**
   * Initializes the desktop menu controller, attaching necessary events.
   * @function
   * @memberof DesktopMenuController
   * @return {void}
   */
  const init = () => {
    attachEvents();
  };

  return {
    init,
  };
};

export default desktopMenuController;
