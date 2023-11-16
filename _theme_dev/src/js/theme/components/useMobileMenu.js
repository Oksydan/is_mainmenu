import getSubMenuRequest from '../request/getSubMenuRequest';
import { on } from '@js/utils/event/EventHandler';
import { parseToHtml } from '@js/utils/DOM/DOMHelpers';
// import mobileMenuController from '../controller/mobileMenuController';

const useMobileMenu = (
  mobileMenuOffcanvasSelector = '#mobile_menu_offcanvas',
  mobileMenuMainWrapper = '.js-mobile-menu',
  mobileMenuTrack = '.js-mobile-menu-track',
  nextLevelBtnSelector = '.js-mobile-menu-tab-link',
  menuListElementSelector = '.js-mobile-menu-list',
  menuBackBtnSelector = '.js-mobile-menu-back-btn',
) => {
  const loadedMenuSet = new Set();
  const menuHistory = [];
  let initialized = false;

  const fetchSubmenu = async (ids) => {
    // getDesktopSubmenuAjaxUrl is a global variable defined in the module ActionFrontControllerSetMedia hook
    const { getRequest } = getSubMenuRequest(getMobileSubmenuAjaxUrl, {
      'id_menu_elements[]': ids,
    });

    return await getRequest();
  }

  const handleMenuOpen = () => {
    if (initialized) {
      return;
    }

    initialized = true;
    const activeList = document.querySelector(`${menuListElementSelector}.active`);
    const ids = getIdsElementsFromList(activeList);

    fetchSubMenuAndRender(ids);
  }

  const attachEvents = () => {
    on(document, 'click', nextLevelBtnSelector, handleClickNextLevelBtn);
    on(document, 'click', menuBackBtnSelector, handleClickBackBtn);

    const offcanvas = document.querySelector(mobileMenuOffcanvasSelector);

    if (offcanvas) {
      offcanvas.addEventListener('show.bs.offcanvas', handleMenuOpen);
    }
  }

  const getDepthToCalc = (depth) => {
    return depth > 0 ? depth - 1 : depth;
  }

  const goToId = (id) => {
    // mobileMenuController().goToId(id, depth);
    const list = document.querySelector(`${menuListElementSelector}[data-id="${id}"]`);

    if (!list) {
      return;
    }

    const depth = list.getAttribute('data-depth');
    list.classList.add('active');
    list.style.left = `${getDepthToCalc(depth) * 100}%`;
    menuHistory.push(id);

    fetchSubMenuAndRender(getIdsElementsFromList(list));
  }

  const moveTrackToDepth = (depth) => {
    // mobileMenuController().moveTrackToDepth(depth);
    const track = document.querySelector(mobileMenuTrack);

    if (!track) {
      return;
    }

    track.style.transform = `translateX(-${getDepthToCalc(depth) * 100}%)`;
  }

  const setLoading = (isLoading) => {
    // mobileMenuController().setLoading(isLoading);
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

  const back = () => {
    // mobileMenuController().back();
    const track = document.querySelector(mobileMenuTrack);

    if (!track) {
      return;
    }

    const lastId = menuHistory.pop();
    const list = document.querySelector(`${menuListElementSelector}[data-id="${lastId}"]`);
    const depth = parseInt(list.getAttribute('data-depth'), 10);

    if (depth && !Number.isNaN(depth)) {
      moveTrackToDepth(depth - 1);
    }

    if (!list) {
      return;
    }

    list.classList.remove('active');

  }

  const handleClickBackBtn = (event) => {
    event.preventDefault();

    back();
  }

  const handleClickNextLevelBtn = async (event) => {
    event.preventDefault();

    const { delegateTarget } = event;
    const id = delegateTarget.getAttribute('data-id');
    const currentDepth = parseInt(delegateTarget.getAttribute('data-depth'), 10);

    if (!id) {
      return;
    }

    if (currentDepth && !Number.isNaN(currentDepth)) {
      moveTrackToDepth(currentDepth + 1);
    }

    goToId(id);
  }

  const getIdsElementsFromList = (list) => {
    const ids = [];

    list.querySelectorAll(nextLevelBtnSelector).forEach((element) => {
      ids.push(element.getAttribute('data-id'));
    });

    return ids;
  }

  const fetchSubMenuAndRender = async (ids) => {
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
    const { html, success } = await fetchSubmenu(idsToFetch);

    if (success) {
      const elements = new DOMParser().parseFromString(html, 'text/html').body.childNodes;

      elements.forEach((element) => {
        document.querySelector(mobileMenuTrack)?.append(element);
      });
    }

    setLoading(false);
  }

  const init = () => {
    attachEvents();
  }

  return {
    init,
  }
}

export default useMobileMenu;
