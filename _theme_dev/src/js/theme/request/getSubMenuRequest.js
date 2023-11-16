import useHttpRequest from '@js/utils/http/useHttpRequest';

const getSubMenuRequest = (url, payload) => {
  const { request } = useHttpRequest(url);

  const getRequest = () => new Promise((resolve, reject) => {
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
