<?php

/**
 *	@author gavinys@gavinys.com
 *	@since 1.0
 *	@date 2016.07.20
 *
 */

class Page {

	/**
	 *	当前page
	 *	@var int
	 *
	 */
	private $page;

	/**
	 *	列表总量
	 *	@var int
	 *
	 */
	private $count;

	/**
	 *	总页数
	 *	@var
	 *
	 */
	private $count_page;

	/**
	 *	数目
	 *	@var int
	 *
	 */
	private $limit;

	/**
	 *	偏移量
	 *	@var int
	 *
	 */
	private $offset;

	/**
	 *	url
	 *	@var string
	 *
	 */
	private $url;

	/**
	 *	模式,0不带querystring,1带querystring
	 *	@var int
	 *
	 */
	private $mode = 1;

	/**
	 *	构造函数
	 *
	 *
	 */
	public function __construct($param)
	{
		if(is_array($param)){
			if(isset($param['page'])){
				$this->page = intval($param['page']) > 0 ? intval($param['page']) : 1;
			} else{
				throw new Exception('page参数缺失');
			}
			if(isset($param['count'])){
				$this->count = intval($param['count']) >= 0 ? intval($param['count']) : 0;
			} else{
				throw new Exception('count参数丢失');
			}
			if(isset($param['limit'])){
				$this->limit = intval($param['limit']) > 0 ? intval($param['limit']) : 1;
			} else{
				throw new Exception('limit参数丢失');
			}
		} else{
			throw new Exception('参数必须为一个数组,array(page,limit,offset,count)');
		}
		$this->checkPageData();
	}

	/**
	 *	检验分页数据是否合法
	 *
	 *
	 */
	private function checkPageData()
	{
		$this->count_page = ceil($this->count / $this->limit);
		$this->offset = ($this->page - 1) * $limit;
		if($this->count_page <= 0){
			throw new Exception("页面数据不合法");
		}
		if($this->offset > $this->count){
			throw new Exception("页面数据不合法");
		}
	}

	/**
	 *	获取分页数据
	 *
	 *
	 *
	 */
	public function pagination()
	{
	    //计算分页信息
	    $prev_page[0] = $this->page - 3 > 0 ? $this->page - 3 : null;
	    $prev_page[1] = $this->page - 2 > 0 ? $this->page - 2 : null;
	    $prev_page[2] = $this->page - 1 > 0 ? $this->page - 1 : null;
	    $prev = $this->page > 1 ? 1 : null;
	    $next_page[0] = $this->page + 1 <= $this->count_page ? $this->page+1 : null;
	    $next_page[1] = $this->page + 2 <= $this->count_page ? $this->page+2 : null;
	    $next_page[2] = $this->page + 3 <= $this->count_page ? $this->page+3 : null;
	    $next = $this->page < $this->count_page ? $this->count_page : null;
	    $array = array(
	        'page' => $this->page,
	        'next_page' => $next_page,
	        'prev_page' => $prev_page,
	        'next' => $next,
	        'prev' => $prev,
	        'limit' => $this->limit,
	        'offset' => $this->offset,
	        'count_page' => $this->count_page,
	        'count' => $this->count
	    );
	    return $array;
	}

	public function bootstrapMode($url, $mode = 1)
	{
		$page = $this->pagination();
		if($mode){
			if(isset($_SERVER["QUERY_STRING"]))
				$query_string = '?'.$_SERVER["QUERY_STRING"];
		} else{
			$query_string = '';
		}
		echo '<div class="row">';
	    echo '<div class="col-sm-5">';
	    echo '<div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing '.$page['offset'].' to '.($page['offset']+$page['limit']).' of '.$page['count'].' entries</div>';
	    echo '</div>';
	    echo '<div class="col-sm-7">';
	    echo '<div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">';
	    echo '<ul class="pagination">';
	    if(!$page['prev']){
	        echo '<li class="paginate_button previous disabled" id="example2_previous"><a href="#" aria-controls="example2" data-dt-idx="0" tabindex="0">Previous</a></li>';
	    } else{
	        echo '<li class="paginate_button previous" id="example2_previous"><a href="'.$url.'/'.$page['prev'].$query_string.'" aria-controls="example2" data-dt-idx="0" tabindex="0">Previous</a></li>';
	    }
	    if($page['prev_page'][0]){
	        echo '<li class="paginate_button"><a href="'.$url.'/'.$page['prev_page'][0].$query_string.'" aria-controls="example2" data-dt-idx="1" tabindex="0">'.$page['prev_page'][0].'</a></li>';
	    }
	    if($page['prev_page'][1]){
	        echo '<li class="paginate_button"><a href="'.$url.'/'.$page['prev_page'][1].$query_string.'" aria-controls="example2" data-dt-idx="1" tabindex="0">'.$page['prev_page'][1].'</a></li>';
	    }
	    if($page['prev_page'][2]){
	        echo '<li class="paginate_button"><a href="'.$url.'/'.$page['prev_page'][2].$query_string.'" aria-controls="example2" data-dt-idx="1" tabindex="0">'.$page['prev_page'][2].'</a></li>';
	    }
	    echo '<li class="paginate_button active"><a href="'.$url.'/'.$page['page'].$query_string.'" aria-controls="example2" data-dt-idx="1" tabindex="0">'.$page['page'].'</a></li>';
	    if($page['next_page'][0]){
	        echo '<li class="paginate_button"><a href="'.$url.'/'.$page['next_page'][0].$query_string.'" aria-controls="example2" data-dt-idx="1" tabindex="0">'.$page['next_page'][0].'</a></li>';
	    }
	    if($page['next_page'][1]){
	        echo '<li class="paginate_button"><a href="'.$url.'/'.$page['next_page'][1].$query_string.'" aria-controls="example2" data-dt-idx="1" tabindex="0">'.$page['next_page'][1].'</a></li>';
	    }
	    if($page['next_page'][2]){
	        echo '<li class="paginate_button"><a href="'.$url.'/'.$page['next_page'][2].$query_string.'" aria-controls="example2" data-dt-idx="1" tabindex="0">'.$page['next_page'][2].'</a></li>';
	    }
	    if(!$page['next']){
	        echo '<li class="paginate_button next disabled" id="example2_next"><a href="#" aria-controls="example2" data-dt-idx="0" tabindex="0">Next</a></li>';
	    } else{
	        echo '<li class="paginate_button next" id="example2_next"><a href="'.$url.'/'.$page['next'].$query_string.'" aria-controls="example2" data-dt-idx="0" tabindex="0">Next</a></li>';
	    }
	    echo '</ul>';
	    echo '</div>';
	    echo '</div>';
	    echo '</div>';
	}

}