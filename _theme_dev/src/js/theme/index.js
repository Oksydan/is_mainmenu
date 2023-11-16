import { DOMReady } from '@js/utils/DOM/DOMHelpers';
import useDesktopMenu from './components/useDesktopMenu';
import useMobileMenu from './components/useMobileMenu';

DOMReady(() => {
  const { init: initDesktopMenu } = useDesktopMenu();
  const { init: initMobileMenu } = useMobileMenu();

  initMobileMenu();
  initDesktopMenu();
});
