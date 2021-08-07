<?php

function recalcTree( $element ) {
	static $counter;
	if( is_array( $element->children ))
		foreach( $element->children as $child ) {
			$child->left = ++$counter;
			recalcTree( $child );
			$child->right = ++$counter;
		}
}

if( empty( $_GET['content'] )) {
	$self = MODUL_SELF.'&layeredit='.intval($_GET['layeredit']);

	$layers = db()->select('content_layer')->relate();
	$layers[0] = 'System Root';

	$scripts = array();
	foreach( iv::get('scripts') as $key => $script)
		$scripts[$key] = $script['name'];

	$layer_templates = globFiles('style/'.$conf->page->style.'/layer/*', false );
	$layer_templates[''] = 'Kein Template';
	$panel_templates = globFiles('style/'.$conf->page->style.'/panel/*', false );
	$panel_templates[''] = 'Kein Template';

	if( !empty( $_GET['frontpage'] ))
		$conf->set( 'page', 'startpage', $_GET['frontpage'] );

	if( !empty( $_GET['movepanel'] )) {
		db()->id_update('content_panel', array( 'layer' => $_GET['parent'] ), $_GET['movepanel'] );
		if( db()->affected_rows ) throw new redirect($self);
	}

	if( !empty( $_REQUEST['parent'] )) {
		// do not allow cyclic references
		$layer = $_GET['layerupdate'] ?: $_GET['movelayer'];
		if( db()->query("SELECT 1 FROM content_layer node JOIN content_layer sub
			ON node.lft < sub.lft AND node.rgt > sub.rgt
			WHERE node.id = %d AND sub.id = %d", $layer, $_REQUEST['parent'] )->value())
					throw new Exception('You can not do that!');
	}

	$rc_layer = new data_controller('content_layer', $self, 'layer');
	$rc_layer->add('name', 'Name', 1, 1, 1, 1);
	$rc_layer->add('status', 'Status', 0, 1, 1, 0, 'select', iv::get('layerstatus'));
	$rc_layer->add('parent', 'Parent', 0, 1, 1, 0, 'select', $layers );
	$rc_layer->add('template', 'Template', 0, 1, 1, 0, 'select', $layer_templates );
	$rc_layer->add('link', 'Link', 0, 1, 1, 0, 'text' );

	$layerchanges = $rc_layer->run();
	if( isset( $_GET['layercreate'] ))
		$self = MODUL_SELF.'&layeredit='.db()->id();

	if( !empty( $_GET['movelayer'] )) {
		db()->id_update('content_layer', array( 'parent' => $_GET['parent'] ), $_GET['movelayer'] );
		$layerchanges = db()->affected_rows;
	}

	$layerlist = db()->select( 'content_layer' )->objects( 'id' );
	$root = new stdClass();

	foreach( $layerlist as $layer )
		if( $layer->parent ) $layerlist[$layer->parent]->children[] = $layer;
		else $root->children[] = $layer;

	if( $layerchanges ) {
		// cool datastructure stuff: http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
		recalcTree( $root );
		$query = array();

		foreach( $layerlist as $layer )
			$query[] = db()->format("(%d, %d, %d)", $layer->id, $layer->left, $layer->right );

		db()->query("INSERT INTO content_layer (id, lft, rgt ) VALUES ".implode(',', $query ).
						" ON DUPLICATE KEY UPDATE lft=VALUES(lft), rgt=VALUES(rgt);");
		throw new redirect($self);
	}

	$rc_panel = new data_controller('content_panel', $self, 'panel');
	$rc_panel->add( 'id', 'ID', 0, 0, 0, 0, 'text' );
	$rc_panel->add( 'name', 'Name', 1, 1, 1, 1, 'text' );
	$rc_panel->add( 'group', 'Panelgroup', 1, 1, 1, 1, 'text' );
	$rc_panel->add( 'status', 'Status', 1, 1, 1, 0, 'select', iv::get('panelstatus'));
	$rc_panel->add( 'layer', 'Layer', 0, 0, 1, 0, 'select', $layers );
	$rc_panel->add( 'script', 'Script', 1, 1, 0, 1, 'select', $scripts );
	$rc_panel->add( 'template', 'Template', 1, 1, 1, 0, 'select', $panel_templates );
	$rc_panel->add( 'prio', 'Priorität', 1, 1, 1, 0, 'text' );
	$rc_panel->option( 'assets/small/brush.png', 'content', 'Inhalt bearbeiten');
	$rc_panel->condition = $_GET['layeredit'] ? 'layer = '.intval($_GET['layeredit']) : 'layer is null';
	$rc_panel->auto['create'] = array( 'layer' => $_GET['layeredit'] );

	if( $rc_panel->run()) throw new redirect( $self );

	if( !empty( $_GET['editform'] )) {
		$view->content( $rc_layer->get_edit($_GET['editform']));
		$view->format = 'plain';
	} else {
		$view->js( 'assets/js/contentmanager.js' );
		$view->content( $grid = new widget_grid());
		$grid[0]->box(template('iv.content.layertree')->render(array(
				'tree' => $root,
				'self' => $self,
				'create' => $rc_layer->get_create(),
				'current' => intval( $_GET['layeredit'] ),
				'panelgroups' => db()->query( "SELECT DISTINCT `group` FROM content_panel" )->values(),
				'startpage' => $conf->page->startpage
		)), 'Layerliste');

//		if( !empty( $_GET['layeredit'])) {
//			$edit = $rc_layer->get_edit($_GET['layeredit']);
//			$grid[1]->box($edit, 'Layer bearbeiten');
//		}

		$panelform = $rc_panel->get_form();
		$panelform->id = 'panelform';

		$liste = $rc_panel->get_list();
		$liste->list->id = 'panellist';
		$liste->list->unshift(new list_column_format(' ',
						'<div class="btn btn-small handle" data-panel="%d"><span class="icon-move"></span></div>', 'id'));

		$grid[1]->box($liste, 'Panelliste');
		$grid[1]->box($panelform, 'Panel '.( empty( $_GET['paneledit'] ) ? 'erstellen' : 'bearbeiten'));
	}
} else {
	$scripts = iv::get('scripts');

	if( !$panel = db()->id_get( 'content_panel', $_GET['content'] )) {
		throw new Exception('Das angeforderte Panel wurde nicht gefunden!');
	} elseif( !$rights->has('script', $panel['script'])) {
		throw new Exception('Sie besitzen keine berechtigung auf dieses Panel zuzugreifen.');
	} elseif( !$script = $scripts[$panel['script']]) {
		throw new Exception('Das angeforderte Script wurde nicht gefunden!');
	} elseif( empty( $script['editor'] )) {
		throw new Exception('Für dieses Panel bestehen keine Möglichkeiten zur Inhaltsbearbeitung.');
	} else {
		define( 'LAYER_SELF', MODUL_SELF.'&layeredit='.$panel['layer'] );
		define( 'EDITOR_SELF', MODUL_SELF.'&content='.$panel['id'] );

		$panelvars = $pvgl = db()->select( 'content_variable', "panel = $panel[id]" )->relate( 'value', 'name' );
		include 'scripts/'.$script['editor'].'.php';

		foreach( $panelvars as $key => $var )
			if( $pvgl[$key] != $var )
				db()->query( "REPLACE INTO content_variable ( panel, name, value ) VALUES ( %d, '%s', '%s' );", $panel['id'], $key, $var );
	}
}
