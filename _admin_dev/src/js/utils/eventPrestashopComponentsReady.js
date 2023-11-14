let callNumber = 0;
const callLimit = 20;

const eventPrestashopComponentsReady = () => {
  const checkComponentsReady = () => {
    if (callNumber >= callLimit) {
      return;
    }

    callNumber += 1;

    if (!(window?.prestashop?.component)) {
      setTimeout(checkComponentsReady, 100);
    } else {
      const event = new Event('PrestashopComponentsReady');
      document.dispatchEvent(event);
    }
  };

  document.addEventListener('DOMContentLoaded', checkComponentsReady);

  const onComponentsReady = (callback) => {
    document.addEventListener('PrestashopComponentsReady', callback);
  }

  return {
    onComponentsReady
  }
}

export default eventPrestashopComponentsReady;
