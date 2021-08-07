<?php

$rewrite = $conf->page->force_rewrite || ( function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()));
$status = $user ?  $user->type & 2 ? '0,3,4' : '0,3' : '0,2';
$panel['current'] = $GLOBALS['layer']['id'];

if( $panel['layer'] )
	$nodes = db()->query("SELECT node.*
		FROM content_layer AS node
		JOIN content_layer AS parent
		ON node.lft BETWEEN parent.lft + 1 AND parent.rgt - 1
		WHERE parent.id = %d AND node.status IN ( %s )", $status, $panel['layer'] )->objects('id');
else
	$nodes = db()->query("SELECT * FROM content_layer WHERE status IN ( %s )", $status)->objects('id');

foreach( $nodes as $node ) {
	$node->link = $rewrite ? urldecode($node->name) : IV_SELF.'page='.$node->id;
	if( empty( $node->parent )) $panel['menu'][] = $node;
	elseif( isset( $nodes[$node->parent] )) $nodes[$node->parent]->children[] = $node;
}
