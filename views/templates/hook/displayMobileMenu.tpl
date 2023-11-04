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
{function mobileMenuList children=[] depth=1 parent=[]}
    <div
        class="mobile-menu__list-wrapper js-mobile-menu-list"
        data-depth="{$depth}"
        {if $parent && $depth > 1}
            id="menu_list_{$parent.id}"
        {/if}
    >

        {if $parent && $depth > 1}
            <div class="mobile-menu__elem">
                <a href="{$parent.url}"
                   title="{$parent.title}"
                   class="mobile-menu__depth-btn mobile-menu__depth-btn--prev js-mobile-menu-back">
                    <span class="mobile-menu__icon icon icon-caret-left"></span>
                </a>
                <a href="{$parent.url}" title="{$parent.title}" class="mobile-menu__link mobile-menu__link--back js-mobile-menu-back">
                    {$parent.title}
                </a>
            </div>
        {/if}

        <div class="mobile-menu__list">
            {foreach $children as $child}
                {if in_array($child.type, ['category', 'custom'])}
                    <div class="mobile-menu__elem">
                        <a href="{$child.url}"
                           title="{$child.title}"
                           data-depth="{$depth}"
                           class="mobile-menu__link mobile-menu__link--{$depth}">
                            {$child.title}
                        </a>
                        {if $child.children}
                            <a href="#menu_list_{$child.id}"
                               title="{$child.title}"
                               data-depth="{$depth}"
                               class="mobile-menu__depth-btn mobile-menu__depth-btn--next js-mobile-menu-tab-link">
                                <span class="mobile-menu__icon icon icon-caret-right"></span>
                            </a>
                        {/if}
                    </div>
                {elseif $child.type == 'banner'}
                    <div class="mobile-menu__elem p-3">
                        {renderSubmenuBanner menuElement=$child}
                    </div>
                {elseif $child.type == 'html'}
                    <div class="mobile-menu__elem p-3">
                        {renderSubmenuHtml menuElement=$child}
                    </div>
                {/if}
            {/foreach}
        </div>
    </div>

    {foreach $children as $child}
        {if $child.children}
            {mobileMenuList parent=$child children=$child.children depth=$depth+1}
        {/if}
    {/foreach}
{/function}


<div class="mobile-menu js-mobile-menu">
    {mobileMenuList children=$menu depth=1}
</div>
