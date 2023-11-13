export const parseToHtml = (str) => {
  const parser = new DOMParser();
  const doc = parser.parseFromString(str, 'text/html');

  return doc.body.children[0];
};

const useAutocomplete = (input, appendTo, formatResult, onSearch, onSelect, options = {}) => {
  let timeoutId;
  const opt = {
    timeout: 300,
    minChars: 3,
    ...options,
  };
  const { timeout, minChars } = opt;
  let open = false;

  const buildAutocompleteContent = (resultArray, value) => {
    const resultNodes = resultArray
      .map((item) => {
        const string = formatResult(item);
        const node = parseToHtml(string);

        node.addEventListener('click', onSelect);

        return node;
      });

    const wrapper = document.createElement('div');
    wrapper.classList.add('menu-product-autocomplete');

    resultNodes.forEach((item) => {
      wrapper.appendChild(item);
    });

    if (resultNodes.length === 0) {
      const node = document.createElement('div');
      node.classList.add('m-3', 'alert', 'alert-warning');
      node.innerText = `No results found for ${value}`;

      wrapper.appendChild(node);
    }

    return wrapper;
  };

  const close = () => {
    appendTo.innerHTML = '';

    // eslint-disable-next-line no-use-before-define
    closeAutocomplete();
  };

  const handleDocumentClick = (e) => {
    const { target } = e;
    const isInput = target === input;

    if (isInput) {
      return;
    }

    close();
  };

  const closeAutocomplete = () => {
    open = false;
    document.removeEventListener('click', handleDocumentClick);
  };

  const openAutocomplete = () => {
    document.addEventListener('click', handleDocumentClick);
  };

  const setAutocompletePosition = (content) => {
    const { height } = input.getBoundingClientRect();
    content.style.top = `${height}px`;
  };

  const handleKeyUp = (e) => {
    if (timeoutId) {
      clearTimeout(timeoutId);
    }
    const { target } = e;
    const { value } = target;

    if (value.length < minChars) {
      close();
      return;
    }

    timeoutId = setTimeout(async () => {
      const result = await onSearch(value);
      const content = buildAutocompleteContent(result, value);
      setAutocompletePosition(content);

      appendTo.replaceChildren(content);

      if (!open) {
        open = true;
        openAutocomplete();
      }
    }, timeout);
  };

  const destroy = () => {
    input.removeEventListener('keyup', handleKeyUp);
    close();
  };

  const init = () => {
    input.addEventListener('keyup', handleKeyUp);
  };

  return {
    close,
    destroy,
    init,
  };
};

export default useAutocomplete;
