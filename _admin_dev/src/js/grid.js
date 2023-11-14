import eventPrestashopComponentsReady from './utils/eventPrestashopComponentsReady';

const { onComponentsReady } = eventPrestashopComponentsReady();

onComponentsReady(() => {
  window.prestashop.component.initComponents(
    [
      'Grid',
    ],
  );

  const menuGrid = new window.prestashop.component.Grid('is_mainmenu_list');

  menuGrid.addExtension(new window.prestashop.component.GridExtensions.AsyncToggleColumnExtension());
  menuGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  menuGrid.addExtension(new window.prestashop.component.GridExtensions.PositionExtension(menuGrid));
  menuGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  menuGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
});
