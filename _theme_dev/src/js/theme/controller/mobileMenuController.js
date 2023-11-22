import { on } from '@js/utils/event/EventHandler';
import moveMenuListHandler from "../handler/moveMenuListHandler";

/**
 * Mobile menu controller
 * @param options
 * @param {string} options.mobileMenuOffcanvasSelector - Mobile menu offcanvas selector
 * @param {string} options.mobileMenuMainWrapper - Mobile menu main wrapper selector
 * @param {string} options.mobileMenuTrack - Mobile menu track selector
 * @param {string} options.nextLevelBtnSelector - Next level btn selector
 * @param {string} options.menuListElementSelector - Menu list element selector
 * @param {string} options.menuBackBtnSelector - Menu back btn selector
 * @param {string} options.menuListActiveClass - Menu list active class
 */
const mobileMenuController = (options = {}) => {
  const {
    mobileMenuOffcanvasSelector,
    mobileMenuMainWrapper,
    mobileMenuTrack,
    nextLevelBtnSelector,
    menuListElementSelector,
    menuBackBtnSelector,
    menuListActiveClass
  } = {
    mobileMenuOffcanvasSelector: '#mobile_menu_offcanvas',
    mobileMenuMainWrapper: '.js-mobile-menu',
    mobileMenuTrack: '.js-mobile-menu-track',
    nextLevelBtnSelector: '.js-mobile-menu-tab-link',
    menuListElementSelector: '.js-mobile-menu-list',
    menuBackBtnSelector: '.js-mobile-menu-back-btn',
    menuListActiveClass: 'active',
    ...options
  }

  let initialized = false;
  const {
    goToMenuList,
    back,
    reset
  } = moveMenuListHandler({
    mobileMenuTrack,
    menuListElementSelector,
    mobileMenuMainWrapper,
    nextLevelBtnSelector,
    menuListActiveClass,
  });


  /**
   * Next level btn click handler
   * @param {Event} event - Click event
   * @return {void}
   */
  const nextLevelBtnClickHandler = async (event) => {
    event.preventDefault();

    const { delegateTarget } = event;
    const id = delegateTarget.getAttribute('data-id');

    if (!id) {
      return;
    }

    goToMenuList(id);
  }

  /**
   * Back btn click handler
   * @param {Event} event - Click event
   * @return {void}
   */
  const backBtnClickHandler = (event) => {
    event.preventDefault();

    back();
  }

  /**
   * Mobile menu open handler
   * @return {void}
   */
  const mobileMenuOpenHandler = () => {
    if (initialized) {
      return;
    }

    initialized = true;
    const activeList = document.querySelector(`${menuListElementSelector}.${menuListActiveClass}`);
    const id = parseInt(activeList.getAttribute('data-id'), 10);

    goToMenuList(id);
  }

  /**
   * Mobile menu closed handler
   */
  const mobileMenuClosedHandler = () => {
    reset();
  }

  /**
   * Attach events to document and mobile menu
   */
  const attachEvents = () => {
    on(document, 'click', nextLevelBtnSelector, nextLevelBtnClickHandler);
    on(document, 'click', menuBackBtnSelector, backBtnClickHandler);

    const offcanvas = document.querySelector(mobileMenuOffcanvasSelector);

    if (offcanvas) {
      offcanvas.addEventListener('show.bs.offcanvas', mobileMenuOpenHandler);
      offcanvas.addEventListener('hidden.bs.offcanvas', mobileMenuClosedHandler);
    }
  }

  /**
   * Init mobile menu controller
   */
  const init = () => {
    attachEvents();
  }

  return {
    init
  }
}

export default mobileMenuController;
