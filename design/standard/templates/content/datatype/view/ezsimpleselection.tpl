{let selected_id_array=$attribute.content
     selected_array=array()
     select_delimiter=cond($attribute.class_content.delimiter|ne(""),$attribute.class_content.delimiter,", ")}
{foreach $attribute.class_content.options as $option}
	{if $selected_id_array|contains($option.identifier)}
		{set $selected_array=$selected_array|append($option.name|wash(xhtml))}
	{/if}
{/foreach}
{$selected_array|implode($select_delimiter)}
{/let}
