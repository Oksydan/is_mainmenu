import { DOMReady } from '@js/utils/DOM/DOMHelpers';
import useDesktopMenu from './components/useDesktopMenu';
import mobileMenuController from './controller/mobileMenuController';

DOMReady(() => {
  const { init: initDesktopMenu } = useDesktopMenu();
  const { init: initMobileMenuController } = mobileMenuController();

  initMobileMenuController();
  initDesktopMenu();
});
