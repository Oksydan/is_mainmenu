{extends file='components/offcanvas.tpl'}

{block name='offcanvas_extra_attribues'}id="mobile_menu_offcanvas"{/block}
{block name='offcanvas_title'}{l s='Menu' d='Shop.Theme.Catalog'}{/block}
{block name='offcanvas_body'}
  {include file='module:is_mainmenu/views/templates/front/mobile-menu-content.tpl'}
{/block}
