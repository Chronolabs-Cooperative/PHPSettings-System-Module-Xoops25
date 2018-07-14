<{if $breadcrumb}>
    <{includeq file="db:system_header.tpl"}>
<{/if}>
<!--Preferences-->
<{if $menu}>
    <div class="xo-catsetting">
        <{foreach item=phpsetting from=$phpsettings}>
            <a class="tooltip" href="admin.php?fct=phpsettings&amp;op=show&amp;confphp_id=<{$phpsettings.id}>" title="<{$phpsetting.name}>">
                <img src="<{$phpsetting.image}>" alt="<{$phpsetting.name}>"/>
                <span><{$phpsetting.name}></span>
            </a>
        <{/foreach}>
        <a class="tooltip" href="admin.php?fct=phpsettings&amp;op=showmod&amp;mod=1" title="<{$smarty.const._AM_SYSTEM_PHPSETTINGS_SETTINGS}>">
            <img src="<{xoAdminIcons xoops/system_mods.png}>" alt="<{$smarty.const._AM_SYSTEM_PHPSETTINGS_SETTINGS}>"/>
            <span><{$smarty.const._AM_SYSTEM_PREFERENCES_SETTINGS}></span>
        </a>
    </div>
<{/if}>
<div class="clear">&nbsp;</div>
<br>


