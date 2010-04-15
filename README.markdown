# RSS Library for CodeIgniter

***

## About

I needed to make an RSS feed on one of my projects but couldn't find a solution that I really liked so I built this.  Hopefully someone else can find it handy too.

## Installation

Copy **libraries/rss2.php** to libraries/ in your application folder.

## Initialization

Load the library in whatever controllers you want to use it using:

`$this->load->library('rss2');`

## Usage

RSS feeds are made up of *channels* and *items*.  In this library, each of those are objects and each has their own properties.

### Channels

Create a new channel with:

`$channel = $this->rss2->new_channel();`

You can set properties of the channel using functions like:

	$channel->atom_link(current_url());
	$channel->set_title("Channel Title");
	$channel->set_link(current_url());
	$channel->set_description("Channel Description");

You can set any attribute of the channel using:

`$channel->set_attribute($key, $value, $additional_attributes=false);`

The *$additional_attributes* param allows you to set an additional string within the rendered XML tag.  For instance, the *set_attribute* function would roughly produce the following:

`<key $additional_attributes>value</key>`

### Items

We get a new item by calling

`$item = $channel->new_item();`

You can set item properties by using functions such as

	$item->set_title("Item Title");
	$item->set_link("http://www.example.com/id");
	$item->set_guid("item_guid");
	$item->set_description("Item Description");
	$item->set_author("author.name@example.com (Author Name)");

You can also set any attribute of the item using:

`$item->set_attribute($key, $value, $additional_attributes=false);`

This will be rendered similarly to the equivilent channel function.

We add the item to the channel using

`$channel->add_item($item);`

### Channel Images

You can add images to an entire channel as well.  

**Note:** To generate valid feeds, you should define your channel image before your items.

To start, do

`$image = $channel->new_image();`

You can set various properties of the imag using functions such as

	$image->set_url("http://path-to-image");
	$image->set_title("Image Title");
	$image->set_link("http://www.example.com");
	$image->set_width("100");
	$image->set_width("100");

You add the image to the channel in the same way that you added items.

`$channel->add_item($image);`

### Rendering the Feed

After you've created your channel, channel image, and items you pack them pack into the rss2 object.

`$this->rss2->pack($channel);`

You can specify the response headers using the following

`header($this->rss2->headers());`

And finally, output the feed:

	echo $this->rss2->render();
	exit();
