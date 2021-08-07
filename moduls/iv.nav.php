<?php

	if( isset( $_GET['manage'] )) {
		$self = MODUL_SELF.'&manage';

		if( isset( $_POST['points'] )) {
			$insert = $delete = array();

			foreach( $_POST['points'] as $file => $category ) {
				if( $category == -1 ) $delete[] = $db->format("'%s'", $file );
				else $insert[] = $db->format("( %d, '%s', %s )", $user->id, $file, intval( $category ) ?: 'NULL' );
			}

			if( count( $insert )) {
				$db->query("REPLACE INTO base_menu_point ( user, modul, category )
					VALUES ".implode(',', $insert));
			}

			if(count( $delete )) {
				$db->query("DELETE FROM base_menu_point
					WHERE user = %d AND modul IN (".implode(',', $delete).")"
					, $user->id );
			}

			throw new redirect($self);
		}

		$rc = new data_controller( 'base_menu_category', $self );
		$rc->add('name', 'Name', 1, 1, 1, 1 );
		$rc->auto['create'] = array( 'user' => $user->id );
		$rc->condition = $db->format('user = %d', $user->id );

		if( $rc->run()) throw new redirect($self);

		$possible = $db->base_menu_category->get('user = ' . $user->id)->relate();
		$possible[-1] = 'Allgemein';
		$possible[0] = 'Versteckt';

		$form = new form( $self );

		foreach( iv::get('moduls') as $mod )
			if( $rights->has('modul', $mod['file'] ))
				$form->select(
					'points['.$mod['file'].']',
					$mod['name'],
					$possible,
					isset( $assignment[$mod['file']] ) ? $assignment[$mod['file']] : -1
				);

		$grid = $view->grid();
		$grid[0]->box( $rc->get_list(), 'Kategorien' );
		$grid[0]->box( $rc->get_form(), 'Kategorie ändern' );
		$grid[1]->box( $form, 'Menü Verwalten' );
	} else {
		$cat = isset( $menu[$_GET['category']] ) ? $menu[$_GET['category']] : $menu[0];
		$view->content( template('iv.nav.list')->render( $cat ));
	}

