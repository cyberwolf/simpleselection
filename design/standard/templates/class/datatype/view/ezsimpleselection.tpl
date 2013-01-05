{let content=$class_attribute.content}

<div class="block">
    <div class="element">
        <label>Multiselect:</label>
        <p>{cond(int($content.is_multiselect)|eq(1),"Yes","No")}</p>
    </div>
    
    <div class="element">
        <label>Output Delimiter:</label>
        <p>{cond($content.delimiter|ne(""),$content.delimiter|wash,"<i>Empty</i><br />(Defaults to a comma)")}</p>
    </div>

    <div class="element">
        <label>Use Checkboxes:</label>
        <p>{cond(int($content.checkbox)|eq(1),"Yes","No")}</p>
    </div>
</div>

<div class="block">    
    <div class="element">
        <label>Options:</label>
        <table class="list" cellspacing="0">
            <tr>
                <th>Name</th>
                <th>Identifier</th>
                <th>Default</th>
            </tr>
        {foreach $content.options as $option}
            <tr>
                <td>{cond($option.name|ne(""),$option.name|wash,"<i>Empty</i>")}</td>
                <td>{cond($option.identifier|ne(""),$option.identifier|wash,"<i>Empty</i>")}</td>
				<td>{$option.default|wash}</td>
            </tr>
        {/foreach}
        </table>
    </div>
</div>

{/let}
