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

{function renderMenuLink menuElement=[]}
  <div>
    <a href="{$menuElement.url}" class="main-menu__sub-link">
        {$menuElement.title}
    </a>
  </div>
{/function}

{function renderProduct menuElement=[]}
  {if !empty($menuElement.product)}
      <a href="{$menuElement.product.url}" class="main-menu__sub-link">
        {$menuElement.product.name}
      </a>
  {/if}
{/function}

{function renderSubmenuColumnContent menuElement=[]}
    {if in_array($child.type, ['category', 'custom', 'cms'])}
        {renderMenuLink menuElement=$child}
    {elseif $child.type === 'banner'}
        {renderSubmenuBanner menuElement=$child}
    {elseif $child.type === 'html'}
        {renderSubmenuHtml menuElement=$child}
    {elseif $child.type === 'product'}
        {renderProduct menuElement=$child}
    {/if}
{/function}

{function renderSubmenuColumn menuElement=[]}
  <div class="col">
      {renderSubmenuColumnContent menuElement=$menuElement}

      {if $child.children}
          <div class="row">
              {foreach $child.children as $child}
                {renderSubmenuColumn menuElement=$child}
              {/foreach}
          </div>
      {/if}
  </div>
{/function}

{function renderSubmenu menuElement=[]}
    <div class="main-menu__sub js-main-menu-sub">
        <div class="container">

            <div class="main-menu__sub-content">
                <div class="main-menu__sub-list row">
                    {foreach $menuElement.children as $child}
                        {renderSubmenuColumn menuElement=$child}
                    {/foreach}
                </div>
            </div>

        </div>
    </div>
{/function}

<div class="d-none d-lg-block col-12 header-top__block header-top__block--menu position-static">
    <div class="main-menu position-relative" id="_desktop_top_menu">
        <ul class="js-main-menu-list main-menu__list row align-items-center justify-content-center mb-0">

            {foreach $menu as $menuElement}
                {if in_array($menuElement.type, ['category', 'custom'])}
                    <li class="main-menu__item main-menu__item--{$menuElement.depth} position-static col-auto {if $menuElement.children}js-manu-with-sub{/if}">
                        <a href="{$menuElement.url}" class="main-menu__link main-menu__link--{$menuElement.depth}">
                            {$menuElement.title}
                        </a>
                        {if $menuElement.children}
                            {renderSubmenu menuElement=$menuElement}
                        {/if}
                    </li>
                {/if}
            {/foreach}

        </ul>
    </div>
</div>
