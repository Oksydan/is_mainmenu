<article
  class="card js-product-miniature p-2"
  data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}"
>
  <div class="row flex-nowrap g-2 align-items-center">
    <div class="col-3">
      <a href="{$product.url}" class="product-miniature__thumb-link">
          {images_block}
            <img
              {if $product.default_image}
                data-full-size-image-url="{$product.default_image.large.url}"
                {generateImagesSources image=$product.default_image size='cart_default'}
                alt="{if !empty($product.default_image.legend)}{$product.default_image.legend}{else}{$product.name|truncate:30:'...'}{/if}"
                width="{$product.default_image.bySize.cart_default.width}"
                height="{$product.default_image.bySize.cart_default.height}"
              {else}
                src="{$urls.no_picture_image.bySize.cart_default.url}"
                alt="{$product.name|truncate:30:'...'}"
                width="{$urls.no_picture_image.bySize.cart_default.width}"
                height="{$urls.no_picture_image.bySize.cart_default.height}"
              {/if}
              class="img-fluid rounded"
              loading="lazy"
            />
          {/images_block}
      </a>
    </div>
    <div class="col">
      <p class="h6 mb-2">
        <a class="text-reset" href="{$product.url}">
          {$product.name}
        </a>
      </p>

      {if $product.show_price}
        {if $product.has_discount}
          {hook h='displayProductPriceBlock' product=$product type="old_price"}
          <span
            class="price price--sm price--regular me-1"
            aria-label="{l s='Regular price' d='Shop.Theme.Catalog'}"
          >
            {$product.regular_price}
          </span>
        {/if}

        {hook h='displayProductPriceBlock' product=$product type="before_price"}

        <span
          class="price price--sm"
          aria-label="{l s='Price' d='Shop.Theme.Catalog'}"
        >
          {$product.price}
        </span>

        {hook h='displayProductPriceBlock' product=$product type='unit_price'}

        {hook h='displayProductPriceBlock' product=$product type='weight'}
      {/if}
    </div>
  </div>
</article>
