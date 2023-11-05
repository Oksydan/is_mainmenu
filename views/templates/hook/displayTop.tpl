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
  <li class="mt-3">
    <a href="{$child.url}" class="main-menu__sub-link">
        {$child.title}
    </a>
  </li>
{/function}

{function renderSubmenuColumnLinks menuElement=[]}
    <ul class="mb-0 mt-3">
        {foreach $menuElement.children as $child}
            {if in_array($child.type, ['category', 'custom', 'cms'])}
                {renderMenuLink menuElement=$child}
            {elseif $child.type === 'banner'}
                {renderSubmenuBanner menuElement=$child}
            {elseif $child.type === 'html'}
                {renderSubmenuHtml menuElement=$child}
            {/if}

            {if $child.children}
                {renderSubmenuColumnLinks menuElement=$child}
            {/if}
        {/foreach}
    </ul>
{/function}

{function renderSubmenuColumn menuElement=[]}
    <p class="main-menu__sub-title text-uppercase mb-lg-4 mb-3 font-weight-semibold">
        <a href="{$menuElement.url}" class="main-menu__sub-link">
            {$menuElement.title}
        </a>
    </p>

    {if $menuElement.children}
        {renderSubmenuColumnLinks menuElement=$menuElement}
    {/if}
{/function}

{function renderSubmenu menuElement=[]}
    <div class="main-menu__sub js-main-menu-sub">
        <div class="container">

            <div class="main-menu__sub-content">
                <div class="main-menu__sub-list row">
                    {foreach $menuElement.children as $child}
                        {if in_array($child.type, ['category', 'custom'])}
                            <div class="col-lg-3 col-xxl-2 {if $child@first}offset-xl-2{/if}">
                                {renderSubmenuColumn menuElement=$child}
                            </div>
                        {elseif $child.type === 'banner'}
                            <div class="col-lg-3 ml-auto">
                                {renderSubmenuBanner menuElement=$child}
                            </div>
                        {elseif $child.type === 'html'}
                            <div class="col-lg-3 ml-auto">
                                {renderSubmenuHtml menuElement=$child}
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>

        </div>
    </div>
{/function}

<div class="d-none d-lg-block col-12 header-top__block header-top__block--menu position-static">
    <div class="main-menu" id="_desktop_top_menu">
        <ul class="js-main-menu-list main-menu__list row align-items-center justify-content-center mb-0">

            {foreach $menu as $menuElement}
                {if in_array($menuElement.type, ['category', 'custom'])}
                    <li class="main-menu__item main-menu__item--{$menuElement.depth} col-auto {if $menuElement.children}js-manu-with-sub{/if}">
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
