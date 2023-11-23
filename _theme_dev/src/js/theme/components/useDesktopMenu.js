import getSubMenuRequest from '../request/getSubMenuRequest';

/**
 * A utility for handling interactions and fetching submenus in a desktop menu.
 * @typedef {Object} UseDesktopMenu
 * @property {HandleMainMenuSubLinkMouseEvent} handleMainMenuSubLinkMouseEvent - Handles mouse events on main menu sub-links.
 * @property {HandleMainMenuSubLinkFetch} handleMainMenuSubLinkFetch - Fetches and displays submenus for main menu sub-links.
 */

/**
 * Handles mouse events on main menu sub-links.
 * @callback HandleMainMenuSubLinkMouseEvent
 * @param {Event} event - The mouse event.
 * @param {HTMLElement} target - The target element of the event.
 * @return {void}
 */

/**
 * Fetches and displays submenus for main menu sub-links.
 * @callback HandleMainMenuSubLinkFetch
 * @param {Event} event - The fetch event.
 * @param {HTMLElement} target - The target element of the event.
 * @return {Promise<void>} A Promise that resolves when the submenu is fetched and displayed.
 */

/**
 * Creates a new instance of UseDesktopMenu.
 * @function
 * @param {Object} options - Options for configuring the UseDesktopMenu.
 * @param {string} options.mainMenuItemSubSelector - Selector for the submenu element within a main menu item.
 * @returns {UseDesktopMenu} A new UseDesktopMenu instance.
 */
const useDesktopMenu = ({
  mainMenuItemSubSelector,
}) => {
  const LOADED_CLASS = 'already-loaded';
  const ACTIVE_CLASS = 'active-submenu';

  /**
   * Handles mouse events on main menu sub-links.
   * @function
   * @memberof UseDesktopMenu
   * @param {Event} event - The mouse event.
   * @param {HTMLElement} target - The target element of the event.
   * @return {void}
   */
  const handleMainMenuSubLinkMouseEvent = (event, target) => {
    const { type } = event;

    if (type === 'mouseover') {
      target?.classList.add(ACTIVE_CLASS);
    } else {
      target?.classList.remove(ACTIVE_CLASS);
    }
  };

  /**
   * Fetches submenu data.
   * @function
   * @inner
   * @param {string} id - The ID of the submenu.
   * @return {Promise<object>} A Promise that resolves with the submenu data.
   */
  const fetchSubmenu = async (id) => {
    // getDesktopSubmenuAjaxUrl is a global variable defined in the module ActionFrontControllerSetMedia hook
    // eslint-disable-next-line no-undef
    const { getRequest } = getSubMenuRequest(getDesktopSubmenuAjaxUrl, {
      id_menu_element: id,
    });

    return getRequest();
  };

  /**
   * Fetches and displays submenus for main menu sub-links.
   * @function
   * @memberof UseDesktopMenu
   * @param {Event} event - The fetch event.
   * @param {HTMLElement} target - The target element of the event.
   * @return {Promise<void>} A Promise that resolves when the submenu is fetched and displayed.
   */
  const handleMainMenuSubLinkFetch = async (event, target) => {
    const id = target?.getAttribute('data-id');

    if (target?.classList.contains(LOADED_CLASS) || !id) {
      return;
    }

    target.classList.add(LOADED_CLASS);
    const { html, success } = await fetchSubmenu(id);

    if (!success) {
      return;
    }

    const submenuContentElement = target?.querySelector(mainMenuItemSubSelector);

    if (submenuContentElement) {
      submenuContentElement.innerHTML = html;
    }
  };

  return {
    handleMainMenuSubLinkMouseEvent,
    handleMainMenuSubLinkFetch,
  };
};

export default useDesktopMenu;
