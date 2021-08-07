<?php

class comments {
	private $id, $blocked, $order, $limit, $pagination, $page, $formTemplate, $commentsTemplate;


	function __construct($id = null, $blocked = false, $formTemplate = 'comment/form', $commentsTemplate = 'comment/comments') {
		$this->id = $id;
		if($id == null) $this->id = PAGE_SELF;

		$this->oder = 'ASC';
		$this->pageination = true;
		$this->limit = 25;
		$this->page = (int)(($arguments[0] == null || $arguments[0] < 1) ? 1 : $arguments[0]);
		$this->page--;

		$this->setFormTemplate($formTemplate);
		$this->setCommentsTemplate($commentsTemplate);

		$this->blocked = $blocked;
		if(iv::get('user') == null) $this->blocked = true;
		if(!$this->blocked && !empty($_POST['form_text_'.md5($this->id)])) {
			$this->addReply($_POST['form_text_'.md5($this->id)]);
		}
	}

	public function setFormTemplate($template) {
		$this->formTemplate = $template;
		return $this;
	}
	public function setCommentsTemplate($template) {
		$this->commentsTemplate = $template;
		return $this;
	}
	public function setPagination($pagination) {
		$this->pagination = (bool)$pagination;
		return $this;
	}
	public function setLimit($limit) {
		$this->limit = $limit;
		return $this;
	}
	public function setFirstToLast() {
		$this->order = "ASC";
		return $this;
	}
	public function setLastToFirst() {
		$this->order = "DESC";
		return $this;
	}
	private function addReply($text) {
		$user = iv::get('user');
		db()->comments->insert(array('thread' => $this->id, 'text' => $text, 'user' => $user->id, 'date' => time()));
	}
	public function get() {
		$pagination = array();
		$comments = db()->query("SELECT SQL_CALC_FOUND_ROWS * FROM comments WHERE thread = '%s' ORDER BY date %s LIMIT %d,%d", $this->id, $this->order, $this->limit*$this->page, $this->limit)->assocs();
		if($comments) {
			if($this->pagination) {
				$numComments = db()->query("SELECT FOUND_ROWS()");
				$numPages = ceil($numComments / $this->limit);
				for($i = 1; $i <= $numPages; $i++) {
					$pagination[] = array('page' => $i, 'url' => PAGE_SELF . $i . '/', 'diff' => abs(($i-1)-$this->page), 'isFirst' => $i == 1, 'isLast' => $i == $numPages, 'isActive' => ($i-1) == $this->page);
				}
			}
			$reqUserData = array();
			for($i = 0; $i < count($comments); $i++) {
				$reqUserData[] = $comments[$i]['user'];
			}
			$users = db()->user_data->get("id IN (".implode(",", array_unique($reqUserData)).")")->assocs("id");
			for($i = 0; $i < count($comments); $i++) {
				$comments[$i]['user'] = $users[$comments[$i]['user']];
				$comments[$i]['user']['emailhash'] = md5($comments[$i]['user']['email']);
			}
		}
		return array("comments" => $comments, "pagination" => $pagination);
	}
	public function renderForm($specialTemplate = null) {
		$view = new view( ($specialTemplate) ? $specialTemplate : $this->formTemplate );
		$view->assign( 'uniqueid', md5($this->id) );
		$view->display();
		return $this;
	}
	public function renderComments($specialTemplate = null) {
		$view = new view( ($specialTemplate) ? $specialTemplate : $this->commentsTemplate );
		$commentData = $this->get();
		$view->assign( 'comments', $commentData['comments'] );
		if($this->pagination) {
			$view->assign( 'pagination', $commentData['pagination'] );
		}
		$view->display();
		return $this;
	}
	public function render() {
		$this->renderForm();
		$this->renderComments(); 
		return $this;
	}
}