import useHttpRequest from '@js/utils/http/useHttpRequest';

/**
 * A utility for making a request to retrieve submenu data.
 * @typedef {Object} GetSubMenuRequest
 * @property {function(): Promise<Object>} getRequest - Executes the submenu request and returns a Promise.
 */

/**
 * Creates a new instance of GetSubMenuRequest.
 * @function
 * @param {string} url - The URL for the submenu request.
 * @param {object} payload - The payload for the submenu request.
 * @returns {GetSubMenuRequest} A new GetSubMenuRequest instance.
 */
const getSubMenuRequest = (url, payload) => {
  const { request } = useHttpRequest(url);

  /**
   * Executes the submenu request and returns a Promise.
   * @function
   * @memberof GetSubMenuRequest
   * @return {Promise<Object>} A Promise that resolves with the response data.
   */
  const getRequest = () => new Promise((resolve) => {
    request
      .query(payload)
      .get()
      .json((resp) => {
        resolve(resp);
      });
  });

  return {
    getRequest,
  };
};

export default getSubMenuRequest;
