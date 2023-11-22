import getSubMenuRequest from '../request/getSubMenuRequest';

/**
 * Typedefs
 * @typedef ServerResponse
 * @type {Object}
 * @property {Object} data
 * @property {Object} data.html - HTML of submenus
 * @property {Object} data.success - Success status
 */

/**
 * Fetch submenu
 * @param {int[]} ids
 * @return {Promise<ServerResponse>}
 */
const fetchSubmenuHandler =  async (ids) => {
  // getMobileSubmenuAjaxUrl is a global variable defined in the module ActionFrontControllerSetMedia hook
  const { getRequest } = getSubMenuRequest(getMobileSubmenuAjaxUrl, {
    'id_menu_elements[]': ids,
  });

  return await getRequest();
}

export default fetchSubmenuHandler;
