{let class_content=$class_attribute.content}
<div class="block">
    <div class="element">
        <label>Output Delimiter:</label>
        <input type="text" name="ContentClass_ezsimpleselection_delimiter_{$class_attribute.id}" value="{$class_content.delimiter|wash}" size="5" />
    </div>
    
    <div class="element">
		<label for="ContentClass_ezsimpleselection_ismultiple_value_{$class_attribute.id}">Multiple choice:</label>
        <input type="checkbox" name="ContentClass_ezsimpleselection_ismultiple_value_{$class_attribute.id}" id="ContentClass_ezsimpleselection_ismultiple_value_{$class_attribute.id}" {section show=$class_content.is_multiselect}checked="checked"{/section} /> 
    </div>

    <div class="element">
		<label for="ContentClass_ezsimpleselection_checkbox_{$class_attribute.id}">Use Checkboxes:</label>
        <input type="checkbox" name="ContentClass_ezsimpleselection_checkbox_{$class_attribute.id}" id="ContentClass_ezsimpleselection_checkbox_{$class_attribute.id}" {section show=$class_content.checkbox}checked="checked"{/section} /> 
    </div>
</div>

<div class="block">
    <div class="element">
        <label>Option String <small>(use ~|~ to separate entries)</small>:</label>
        <input type="text" name="ContentClass_ezsimpleselection_options_{$class_attribute.id}" value="{$class_content.option_string|wash}" size="50" />
    </div>
</div>

<div class="block">
    <div class="element">
        <label>Default Option Values <small>(use ~|~ to separate entries)(use 'yes' or 'no' for defaults)</small>:</label>
        <input type="text" name="ContentClass_ezsimpleselection_option_values_{$class_attribute.id}" value="{$class_content.option_string_values|wash}" size="50" />
    </div>
</div>

{/let}
