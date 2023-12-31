{function renderSubmenuHtml menuElement=[]}
  <div class="menu-html-content">
    {$menuElement.content nofilter}
  </div>
{/function}

{function renderSubmenuBanner menuElement=[]}
  {if $menuElement.banner}
    <div class="menu-banner">
      <a href="{$menuElement.url}" class="menu-banner__link">
        <p class="menu-banner__title text-truncate">
          {$menuElement.title}
        </p>
        <img
          src="{$menuElement.banner.imageSrc}"
          class="menu-banner__img img-fluid"
          width="{$menuElement.banner.imageSizes.width}"
          height="{$menuElement.banner.imageSizes.height}"
          alt="{$menuElement.banner.alt}"
          loading="lazy"
        >
      </a>
    </div>
  {/if}
{/function}

{function renderSubmenuLinks menuElement=[]}
  <a href="{$child.url}"
     title="{$child.title}"
     data-depth="{$depth}"
     class="f-mobile-menu__link f-mobile-menu__link--{$depth}">
      {$child.title}
  </a>
  {if $child.has_children}
    <a href="#menu_list_{$child.id}"
       title="{$child.title}"
       data-depth="{$depth}"
       data-id="{$child.id}"
       class="f-mobile-menu__depth-btn f-mobile-menu__depth-btn--next js-f-mobile-menu-tab-link">
          <span class="material-icons f-mobile-menu__icon p-2">
            chevron_right
          </span>
    </a>
  {/if}
{/function}

{function renderSubmenuProduct menuElement=[]}
  {if !empty($menuElement.product)}
    {include
      file='module:is_mainmenu/views/templates/front/_partials/mobile-product.tpl'
      product=$menuElement.product
    }
  {/if}
{/function}

{function renderMenuElement menuElement=[]}
  {if in_array($menuElement.type, ['category', 'custom', 'cms'])}
    <div class="f-mobile-menu__elem">
      {renderSubmenuLinks menuElement=$menuElement}
    </div>
  {elseif $menuElement.type == 'banner'}
    <div class="f-mobile-menu__elem py-3">
      {renderSubmenuBanner menuElement=$menuElement}
    </div>
  {elseif $menuElement.type == 'html'}
    <div class="f-mobile-menu__elem py-3">
      {renderSubmenuHtml menuElement=$menuElement}
    </div>
  {elseif $menuElement.type == 'product'}
    <div class="f-mobile-menu__elem py-3">
      {renderSubmenuProduct menuElement=$menuElement}
    </div>
  {/if}
{/function}

{function mobileMenuList menu=[] depth=1 parent=[]}
  <div
    class="f-mobile-menu__list-wrapper js-f-mobile-menu-list {if $depth <= 1}active{/if}"
    data-depth="{$depth}"

    {if $parent && $depth > 1}
      data-id="{$parent.id}"
    {else}
      data-id="0"
    {/if}
  >

    {if $parent && $depth > 1}
      <div class="f-mobile-menu__elem">
        <a href="#"
           title="{$parent.title}"
           data-id="{$parent.id}"
           class="f-mobile-menu__depth-btn f-mobile-menu__depth-btn--prev js-f-mobile-menu-back-btn"
        >
          <span class="material-icons f-mobile-menu__icon p-2">
              chevron_left
          </span>
        </a>
        <a href="#"
           title="{$parent.title}"
           data-id="{$parent.id}"
           class="f-mobile-menu__link f-mobile-menu__link--back js-f-mobile-menu-back-btn">
            {$parent.title}
        </a>
      </div>
    {/if}

    <div class="f-mobile-menu__list">
      {foreach $menu as $child}
          {renderMenuElement menuElement=$child}
      {/foreach}
    </div>

    {if $depth <= 1}
      <div class="js-top-menu-bottom">
        <div id="_mobile_currency_selector" class="mb-2"></div>
        <div id="_mobile_language_selector" class="mb-2"></div>
        <div id="_mobile_contact_link" class="mb-2"></div>
      </div>
    {/if}
  </div>
{/function}

{mobileMenuList menu=$menu depth=$depth|default:1 parent=$parent|default:[]}
