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

{if !empty($menu_tree)}
  <div class="container">

    <div class="main-menu__sub-content">
      <div class="main-menu__sub-list row">
          {foreach $menu_tree as $child}
              {renderSubmenuColumn menuElement=$child}
          {/foreach}
      </div>
    </div>
  </div>
{/if}

