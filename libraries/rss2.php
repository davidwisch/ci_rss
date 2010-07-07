<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* CodeIgniter library for creating RSS feeds.
*
* @author David Wischhusen <davewisch@gmail.com>
*/

/**
* Rss2 class
*/
class Rss2{

	public $channel;

	/**
	* Rss2 constructor
	*
	* @access public
	*/
	public function Rss2(){
		$this->channel = NULL;
	}

	/**
	* Returns a new channel pbject
	*
	* @access public
	* @return Rss2_channel
	*/
	public function new_channel(){
		return new Rss2_channel();
	}

	/**
	* Places a channel in the feed
	*
	* @access public
	* @param Rss2_channel
	*/
	public function pack($channel){
		$this->channel = $channel;
	}

	/**
	* Returns the headers for use with the feed
	*
	* @access public
	* @return string
	*/
	public function headers(){
		return 'Content-Type: application/xml';
	}

	/**
	* Transforms feed to XML
	*
	* @access public
	* @return string
	*/
	public function render(){
		$xml = array();
		$xml[] = '<?xml version="1.0"?>';
		$xml[] = '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
		$xml[] = $this->channel->render();
		$xml[] = '</rss>';

		return implode($xml);
	}
}

/**
* Rss2_channel class
*/
class Rss2_channel{
	public $attributes;
	public $items; //includes images
	public $atom_link;

	/**
	* Rss2_channel constructor
	*
	* @access public
	*/
	public function Rss2_channel(){
		$this->attributes = array();
		$this->items = array();
		$this->atom_link = false;
	}

	/**
	* Returns a new Rss2_item object
	*
	* @access public
	* @return Rss2_item
	*/
	public function new_item(){
		return new Rss2_item();
	}

	/**
	* Returns a new Rss2_image object
	*
	* @access public
	* @return Rss2_image
	*/
	public function new_image(){
		return new Rss2_image();
	}

	/**
	* Adds an item to this channel (Rss2_item or Rss2_image)
	*
	* @access public
	* @param mixed
	*/
	public function add_item($item){
		$this->items[] = $item;
	}

	/**
	* Sets the atom link of this channel
	*
	* @access public
	* @param string
	*/
	public function atom_link($href){
		$this->atom_link = $href;
	}

	/**
	* Sets the channel title
	*
	* @access public
	* @param string
	*/
	public function set_title($title){
		$this->_set_attribute('title', $title);
	}

	/**
	* Sets the channel link
	*
	* @access public
	* @param string
	*/
	public function set_link($link){
		$this->_set_attribute('link', $link);
	}

	/**
	* Sets the channel description
	*
	* @access public
	* @param string
	*/
	public function set_description($description){
		$this->_set_attribute('description', $description);
	}

	/**
	* Generic method for setting an attribute
	*
	* @access public
	* @param string
	* @param string
	* @param mixed
	*/
	public function set_attribute($key, $value, $attributes=false){
		$this->_set_attribute($key, $value, $attributes);
	}

	/**
	* Private function for setting an attribute
	*
	* @access private
	* @param string
	* @param string
	* @param string
	*/
	private function _set_attribute($key, $value, $attributes=false){
		$c = new stdClass;
		$c->key = $key;
		$c->value = $value;
		$c->attributes = $attributes;
		$this->attributes[] = $c;
	}

	/**
	* Returns the channel as XML
	*
	* @access public
	* @return string
	*/
	public function render(){
		$xml = array();
		$xml[] = '<channel>';
		//render channel elements
		if($this->atom_link !== false){
			$xml[] = '<atom:link href="'.$this->atom_link.'" rel="self" type="application/rss+xml" />';
		}
		foreach($this->attributes as $attribute){
			$str = '<'.$attribute->key;
			if(!empty($attribute->attributes)){
				if(is_array($attribute->attributes)){
					foreach($attribute->attributes as $attr){
						$str .= " $attr";
					}
				}
				else{
					$str .= ' '.$attribute->attributes;;
				}
			}
			$str .= '>'; //end opening tag
			$str .= '<![CDATA[ '.nl2br($attribute->value).']]>';
			$str .= '</'.$attribute->key.'>';
			$xml[] = $str;
		}
		//render item elements
		foreach($this->items as $item){
			$xml[] = $item->render();
		}

		$xml[] = '</channel>';
		return implode($xml);
	}
}

/**
* Rss2_item object
*/
class Rss2_item{
	public $attributes;

	/**
	* Rss2_item constructor
	*
	* @access public
	*/
	public function Rss2_item(){
		$this->attributes = array();
	}

	/**
	* Sets the item title
	*
	* @access public
	* @param string
	*/
	public function set_title($title){
		$this->_set_attribute('title', $title);
	}

	/**
	* Sets the item link
	*
	* @access public
	* @param string
	*/
	public function set_link($link){
		$this->_set_attribute('link', $link);
	}

	/**
	* Sets the item guid
	*
	* @access public
	* @param string
	* @param bool
	*/
	public function set_guid($guid, $is_permalink=false){
		$attribute = false;
		if(!$is_permalink){
			$attribute = 'isPermaLink="false"';
		}
		$this->_set_attribute('guid', $guid, $attribute);
	}

	/**
	* Sets the item description
	*
	* @access public
	* @param string
	*/
	public function set_description($description){
		$this->_set_attribute('description', $description);
	}

	/**
	* Sets the item author
	*
	* @access public
	* @param string
	*/
	public function set_author($author){
		$this->_set_attribute('author', $author);
	}

	/**
	* Generic function for adding an item attribute
	*
	* @access public
	* @param string
	* @param string
	* @param mixed
	*/
	public function set_attribute($key, $value, $attributes=false){
		$this->_set_attribute($key, $value, $attributes);
	}

	/**
	* Internal function for setting an item attribute
	*
	* @access private
	* @param string
	* @param string
	* @param mixed
	*/
	private function _set_attribute($key, $value, $attributes=false){
		$c = new stdClass;
		$c->key = $key;
		$c->value = $value;
		$c->attributes = $attributes;
		$this->attributes[] = $c;
	}

	/**
	* Returns the item as XML
	*
	* @access public
	* @return string
	*/
	public function render(){
		$xml = array();
		$xml[] = '<item>';
		foreach($this->attributes as $attribute){
			$str = '<'.$attribute->key;
			if(!empty($attribute->attributes)){
				if(is_array($attribute->attributes)){
					foreach($attribute->attributes as $attr){
						$str .= " $attr";
					}
				}
				else{
					$str .= ' '.$attribute->attributes;
				}
			}
			$str .= '>';
			$str .= '<![CDATA[ '.nl2br($attribute->value).']]>';
			$str .= '</'.$attribute->key.'>';
			$xml [] = $str;
		}
		$xml[] = '</item>';
		return implode($xml);
	}
}

/**
* Rss2_image object
*/
class Rss2_image{
	public $attributes;

	/**
	* Rss2_image constructor
	*
	* @access public
	*/
	public function Rss2_image(){
		$this->attributes = array();
	}

	/**
	* Sets the image URL
	*
	* @access public
	* @param string
	*/
	public function set_url($url){
		$this->_set_attribute('url', $url);
	}

	/**
	* Sets the image title
	*
	* @access public
	* @param string
	*/
	public function set_title($title){
		$this->_set_attribute('title', $title);
	}

	/**
	* Sets the image link
	*
	* @access public
	* @param string
	*/
	public function set_link($link){
		$this->_set_attribute('link', $link);
	}

	/**
	* Sets the image width
	*
	* @access public
	* @param string
	*/
	public function set_width($width){
		$this->_set_attribute('width', $width);
	}

	/**
	* Sets the image height
	*
	* @access public
	* @param string
	*/
	public function set_height($height){
		$this->_set_attribute('height', $height);
	}

	/**
	* Internal function for setting image attributes
	*
	* @access private
	* @param string
	* @param string
	*/
	private function _set_attribute($key, $value){
		$c = new stdClass;
		$c->key = $key;
		$c->value = $value;
		$this->attributes[] = $c;
	}

	/**
	* Return the image as XML
	*
	* @access public
	* @return string
	*/
	public function render(){
		$xml = array();
		$xml[] = '<image>';
		foreach($this->attributes as $attribute){
			$str = '<'.$attribute->key.'>';
			$str .= '<![CDATA[ '.nl2br($attribute->value).']]>';
			$str .= '</'.$attribute->key.'>';
			$xml [] = $str;
		}
		$xml[] = '</image>';
		return implode($xml);
	}
}

/* End of file rss2.php */
