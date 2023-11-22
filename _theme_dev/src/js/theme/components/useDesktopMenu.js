import useHoverIntent from './useHoverIntent';
import getSubMenuRequest from '../request/getSubMenuRequest';

const useDesktopMenu = (
  mainMenuListSelector = '.js-main-menu-list',
  mainMenuItemSelector = '.js-main-menu-item',
  mainMenuItemSubSelector = '.js-main-menu-sub',
) => {
  const handleMainMenuSubLinkMouseEvent = (event, target) => {
    const { type } = event;

    if (type === 'mouseover') {
      target?.classList.add('active-submenu');
    } else {
      target?.classList.remove('active-submenu');
    }
  };

  const fetchSubmenu = async (id) => {
    // getDesktopSubmenuAjaxUrl is a global variable defined in the module ActionFrontControllerSetMedia hook
    // eslint-disable-next-line no-undef
    const { getRequest } = getSubMenuRequest(getDesktopSubmenuAjaxUrl, {
      id_menu_element: id,
    });

    return getRequest();
  };

  const handleMainMenuSubLinkFetch = async (event, target) => {
    const id = target?.getAttribute('data-id');

    if (target?.classList.contains('already-loaded') || !id) {
      return;
    }

    target.classList.add('already-loaded');
    const { html, success } = await fetchSubmenu(id);

    if (!success) {
      return;
    }

    const submenuContentElement = target?.querySelector(mainMenuItemSubSelector);

    if (submenuContentElement) {
      submenuContentElement.innerHTML = html;
    }
  };

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

  const init = () => {
    attachEvents();
  };

  return {
    init,
  };
};

export default useDesktopMenu;
