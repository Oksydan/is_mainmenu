<div class="d-none d-lg-block col-12 header-top__block header-top__block--menu position-static">
    <div class="f-main-menu position-relative">
        <ul class="js-main-menu-list main-menu__list row align-items-center justify-content-center mb-0">
            {foreach $menu as $menuElement}
                {if in_array($menuElement.type, ['category', 'custom', 'cms'])}
                    <li
                      class="f-main-menu__item f-main-menu__item--{$menuElement.depth} position-static col-auto {if $menuElement.has_children}js-main-menu-item{/if}"
                      data-id="{$menuElement.id}"
                    >
                        <a href="{$menuElement.url}"
                           class="f-main-menu__link f-main-menu__link--{$menuElement.depth}"
                        >
                            {$menuElement.title}
                        </a>

                        {if $menuElement.has_children}
                          <div class="f-main-menu__sub js-main-menu-sub">
                          </div>
                        {/if}
                    </li>
                {/if}
            {/foreach}

        </ul>
    </div>
</div>
