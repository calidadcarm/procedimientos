ALTER TABLE glpi_plugin_procedimientos_condicions ADD line_id_1 tinyint(11) NOT NULL DEFAULT '0';
ALTER TABLE glpi_plugin_procedimientos_condicions ADD line_id_2 tinyint(11) NOT NULL DEFAULT '0';
ALTER TABLE glpi_plugin_procedimientos_condicions ADD line_id_3 tinyint(11) NOT NULL DEFAULT '0';
ALTER TABLE glpi_plugin_procedimientos_condicions ADD line_id_4 tinyint(11) NOT NULL DEFAULT '0';
ALTER TABLE glpi_plugin_procedimientos_condicions ADD line_id_5 tinyint(11) NOT NULL DEFAULT '0';
ALTER TABLE glpi_plugin_procedimientos_saltos ADD goto_id int(11) NOT NULL DEFAULT '0';


UPDATE glpi_plugin_procedimientos_condicions AS a 
LEFT join glpi_plugin_procedimientos_procedimientos_items b 
on a.id = b.items_id and b.itemtype='PluginProcedimientosCondicion' 
LEFT join glpi_plugin_procedimientos_procedimientos_items c
on b.plugin_procedimientos_procedimientos_id=c.plugin_procedimientos_procedimientos_id and a.way_yes=c.line
LEFT join glpi_plugin_procedimientos_procedimientos_items d
on b.plugin_procedimientos_procedimientos_id=d.plugin_procedimientos_procedimientos_id and a.way_no=d.line
 SET 
a.id_1 = c.id,
a.tag_id_1=a.tag_0,
a.line_id_1=a.way_yes,
a.id_2 = d.id,
a.tag_id_2=a.tag_1,
a.line_id_2=a.way_no
where c.id is not null;


UPDATE glpi_plugin_procedimientos_saltos AS a 
LEFT join glpi_plugin_procedimientos_procedimientos_items b 
on a.id = b.items_id and b.itemtype='PluginProcedimientosSalto' 
LEFT join glpi_plugin_procedimientos_procedimientos_items c
on b.plugin_procedimientos_procedimientos_id=c.plugin_procedimientos_procedimientos_id and a.goto=c.line
 SET 
a.goto_id = c.id
where c.id is not null;