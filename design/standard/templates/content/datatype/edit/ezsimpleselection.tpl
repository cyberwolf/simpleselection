{default attribute_base="ContentObjectAttribute"}
{def $selected_id_array=$attribute.content}
{if $attribute.class_content.checkbox}
<table>
{for 0 to sub(count($attribute.class_content.options),1) as $i}
{let option=$attribute.class_content.options[$i]}
<td>
<input type="{if $attribute.class_content.is_multiselect}checkbox{else}radio{/if}" name="{$attribute_base}_ezsimpleselection_selected_array_{$attribute.id}[]" {if $selected_id_array|contains($option.identifier)}checked="checked"{/if} value="{$option.name|wash(xhtml)}" {if is_set($tabindex)}tabindex="{$tabindex}"{/if}/>{$option.name|wash(xhtml)}
</td>
{if eq($i|mod(2),0)}
</tr><tr>
{/if}
{/let}
{/for}
</tr>
</table>
{else}
<select class="box {$attribute.object.class_identifier}_{$attribute.contentclass_attribute_identifier}" name="{$attribute_base}_ezsimpleselection_selected_array_{$attribute.id}[]" {section show=$attribute.class_content.is_multiselect}multiple="multiple"{/section} class="box" {if and(is_set($size),$size|gt(0))}size="{$size}"{/if} {if is_set($tabindex)}tabindex="{$tabindex}"{/if}>
    {section name=Option loop=$attribute.class_content.options}
    <option value="{$Option:item.identifier|wash(xhtml)}" {section show=$selected_id_array|contains($Option:item.identifier)}selected="selected"{/section}>{$Option:item.name|wash(xhtml)}</option>
    {/section}
</select>
{/if}
{/default}
