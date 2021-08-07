<?php


class data_pagination {
	private $link;
	private $prefix;
	private $size;
	private $total;
	private $current;

	public function __construct( $link, $size = 25, $prefix = '' ) {
		$this->link = $link;
		$this->prefix = $prefix;
		$this->current = $_GET[$prefix.'page'];
		$this->size = $size;
	}

	public static function pagination( html_link $link, $total, $current, $size = 25, $prefix = '' ) {
		if( $total <= $size ) return false;

		$pages = ceil( $total / $size );
		$links = array();

		for( $i = 0; $i < $pages; $i++ ) {
			$links[$i+1] = $link->pure(array( $prefix.'page' => $i ));
		}

		return template('iv.pagination')->render(array(
			'total' => $total,
			'current' => $current,
			'pages' => $pages,
			'size' => $size,
			'links' => $links
		));
	}

	public function query($sql) {
		if(func_num_args() > 1 )
			$sql = db()->formatSQL( func_get_args());

		$offset = $this->current*$this->size;
		$limit = $this->size;

		$sql = str_replace( 'SELECT', 'SELECT SQL_CALC_FOUND_ROWS', $sql );
		$sql = rtrim( $sql, "\r\n\t ;" )." LIMIT $offset, $limit";

		$res = db()->query( $sql );
		$this->total = db()->query( 'SELECT FOUND_ROWS()' )->value();

		return $res;
	}

	public function get() {
		return self::pagination(
				$this->link,
				$this->total,
				$this->current,
				$this->size,
				$this->prefix);
	}
} 