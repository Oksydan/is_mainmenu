import useHttpRequest from '@js/utils/http/useHttpRequest';

/**
 * Get submenu request
 * @param {string} url - Request URL
 * @param {object} payload - Request payload
 */
const getSubMenuRequest = (url, payload) => {
  const { request } = useHttpRequest(url);

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
