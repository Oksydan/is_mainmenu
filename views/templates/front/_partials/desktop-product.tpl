<article
  class="product-miniature card js-product-miniature p-2 {block name='product_miniature_item_class'}{/block}"
  data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}"
>
    {include file='catalog/_partials/miniatures/_partials/product-thumb.tpl' thumbExtraClass='mb-2'}

    {include file='catalog/_partials/miniatures/_partials/product-title.tpl'}

    {include file='catalog/_partials/miniatures/_partials/product-prices.tpl'}

    {block name='product_form'}
        {include file='catalog/_partials/miniatures/_partials/product-form.tpl'}
    {/block}
</article>
