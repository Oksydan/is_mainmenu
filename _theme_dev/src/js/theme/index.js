import { DOMReady } from '@js/utils/DOM/DOMHelpers';
import desktopMenuController from './controller/desktopMenuController';
import mobileMenuController from './controller/mobileMenuController';

DOMReady(() => {
  const { init: initDesktopMenuController } = desktopMenuController();
  const { init: initMobileMenuController } = mobileMenuController();

  initMobileMenuController();
  initDesktopMenuController();
});
