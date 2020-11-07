<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
 <head>
  <title>Index of {$dir}</title>
  <link rel="stylesheet" type="text/css" href="/.style.css" title="Default" />
 </head>
 <body>
<h1>Index of {$dir}</h1>
{if $gallery != 1}
<table>
<tr><th style="width: 20px;"></th><th>Name</th><th style="width: 8em;">Last modified</th><th style="width: 4em;">Size</th><th style="width: 15em;">Description</th></tr>
<tr><td valign="top"><img src="/icons/back.png" alt="[DIR]"></td><td><a href="..">Parent Directory</a>       </td><td>&nbsp;</td><td align="right">  - </td><td></td></tr>
{/if}
{loop $files}
{if $_.gallery == 1}
<a class="img" href="{$url}" style="background: url({thumbnail $path 175 175}); background-repeat: no-repeat; background-position:center bottom;">{$name}<br />{$desc}</a>{else}
<tr><td><img src="/icons/{$icon}.png" alt="{$alt}"></td><td><a href="{$url}">{$name}</a></td><td>{$time}</td><td>{$size|sanesize}</td><td>{$desc}</td></tr>{/if}
{/loop}
{if $gallery != 1}
</table>
{/if}
</body></html>
