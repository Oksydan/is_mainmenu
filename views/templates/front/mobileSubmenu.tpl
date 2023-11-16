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
{function mobileMenuList menu=[] depth=1 parent=[]}
  <div
    class="mobile-menu__list-wrapper js-mobile-menu-list {if $depth <= 1}active{/if}"
    data-depth="{$depth}"

    {if $parent && $depth > 1}
      data-id="{$parent.id}"
    {else}
      data-id="0"
    {/if}
  >

      {if $parent && $depth > 1}
        <div class="mobile-menu__elem">
          <a href="#"
             title="{$parent.title}"
             data-id="{$parent.id}"
             class="mobile-menu__depth-btn mobile-menu__depth-btn--prev js-mobile-menu-back-btn"
          >
            <span class="material-icons mobile-menu__icon p-2">
                chevron_left
            </span>
          </a>
          <a href="#"
             title="{$parent.title}"
             data-id="{$parent.id}"
             class="mobile-menu__link mobile-menu__link--back js-mobile-menu-back-btn">
              {$parent.title}
          </a>
        </div>
      {/if}

    <div class="mobile-menu__list">
        {foreach $menu as $child}
            {if in_array($child.type, ['category', 'custom', 'cms'])}
              <div class="mobile-menu__elem">
                <a href="{$child.url}"
                   title="{$child.title}"
                   data-depth="{$depth}"
                   class="mobile-menu__link mobile-menu__link--{$depth}">
                    {$child.title}
                </a>
                  {if $child.has_children}
                    <a href="#menu_list_{$child.id}"
                       title="{$child.title}"
                       data-depth="{$depth}"
                       data-id="{$child.id}"
                       class="mobile-menu__depth-btn mobile-menu__depth-btn--next js-mobile-menu-tab-link">
                      <span class="material-icons mobile-menu__icon p-2">
                        chevron_right
                      </span>
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
{/function}

{mobileMenuList menu=$menu depth=$depth|default:1 parent=$parent|default:[]}
