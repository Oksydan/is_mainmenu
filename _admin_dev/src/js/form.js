import useAutocomplete from './components/useAutocomplete';
import eventPrestashopComponentsReady from './utils/eventPrestashopComponentsReady';

const { onComponentsReady } = eventPrestashopComponentsReady();


const initAutoComplete = () => {
  const input = document.querySelector('#menu_element_product_product_query');
  const idProductInput = document.querySelector('#menu_element_product_id_product');
  const idProductAttributeInput = document.querySelector('#menu_element_product_id_product_attribute');
  const appendToElement = document.querySelector('.js-autocomplete-product-result');
  const selectedElement = document.querySelector('.js-autocomplete-product-selected');

  if (!input || !appendToElement || !selectedElement) {
    return;
  }

  const formatResult = (item) => `
      <div
      class="menu-product-autocomplete__item"
      data-name="${item.name}"
      data-id-product="${item.id_product}"
      data-id-product-attribute="${item.id_product_attribute}"
      >
        ${item.image ? `<img class="menu-product-autocomplete__image" src="${item.image}" />` : ''}
        <div class="menu-product-autocomplete__name">
          ${item.name}
        </div>
      </div>
    `;
  const onSearch = async (query) => {
    const url = input.dataset.autocompleteUrl;

    return fetch(url, {
      method: 'POST',
      headers: {
        accept: 'application/json',
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: `q=${encodeURIComponent(query)}`,
    })
      .then((response) => response.json());
  };

  const getSelectedProductTemplate = async (idProduct, idProductAttribute) => {
    const url = input.dataset.selectedProductUrl;

    return fetch(url, {
      method: 'POST',
      headers: {
        accept: 'application/json',
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: `id_product=${encodeURIComponent(idProduct)}&id_product_attribute=${encodeURIComponent(idProductAttribute)}`,
    })
      .then((response) => response.json());
  };

  const onSelect = async (e) => {
    const { currentTarget } = e;
    const idProduct = currentTarget.getAttribute('data-id-product');
    const idProductAttribute = currentTarget.getAttribute('data-id-product-attribute');

    idProductInput.value = idProduct;
    idProductAttributeInput.value = idProductAttribute;
    input.value = '';
    const selectedProduct = await getSelectedProductTemplate(idProduct, idProductAttribute);

    selectedElement.innerHTML = selectedProduct.content;
    // eslint-disable-next-line no-use-before-define
    close();
  };

  const {
    close,
    init,
  } = useAutocomplete(
    input,
    appendToElement,
    formatResult,
    onSearch,
    onSelect,
  );

  init();
};

const initFormComponents = () => {
  window.prestashop.component.initComponents(
    [
      'TranslatableField',
      'TinyMCEEditor',
      'TranslatableInput',
    ],
  );

  const choiceTree = new window.prestashop.component.ChoiceTree('.js-choice-tree-container');

  choiceTree.enableAutoCheckChildren();
};

onComponentsReady(() => {
  initFormComponents();
  initAutoComplete();
});
