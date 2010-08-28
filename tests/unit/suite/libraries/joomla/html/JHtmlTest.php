<?php
require_once 'PHPUnit/Framework.php';

require_once JPATH_BASE. DS . 'libraries' . DS . 'joomla' . DS . 'filesystem' . DS . 'path.php';
require_once JPATH_BASE. DS . 'libraries' . DS . 'joomla' . DS . 'html' . DS . 'html.php';

/**
 * Test class for JHtml.
 * Generated by PHPUnit on 2009-10-27 at 15:36:23.
 */
class JHtmlTest extends JoomlaTestCase
{
	/**
	 * @var JHtml
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->saveFactoryState();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();
	}

	/**
	 * @todo Implement test_().
	 */
	public function test_()
	{
		// first we test to ensure that if a handler is properly registered, it gets called
		$registered = $this->getMock('MyHtmlClass', array('mockFunction'));

		// test that we can register the method
		JHtml::register('file.testfunction', array($registered, 'mockFunction'));

		// test that calling _ actually calls the function
		$registered->expects($this->once())
			->method('mockFunction')
			->with('Test Return Value')
			->will($this->returnValue('My Expected Return Value'));

		$this->assertThat(
			JHtml::_('file.testfunction', 'Test Return Value'),
			$this->equalTo('My Expected Return Value')
		);

		// we unregister the method to return to our original state
		JHtml::unregister('prefix.file.testfunction');

		// now we test with a class that will be found in the expected file
		JHtml::addIncludePath(array(JPATH_BASE.'/tests/unit/suite/libraries/joomla/html/htmltests'));

		$this->assertThat(
			JHtml::_('mocktest.method1', 'argument1', 'argument2'),
			$this->equalTo('JHtml Mock Called')
		);

		$this->assertThat(
			JHtmlMockTest::$arguments[0],
			$this->equalTo(array('argument1', 'argument2'))
		);
		JHtmlMockTest::$arguments = array();

		$this->saveErrorHandlers();
		$mock1 = $this->getMock('errorCallback', array('error1', 'error2', 'error3'));

		JError::setErrorHandling(E_ERROR, 'callback', array($mock1, 'error1'));

		$mock1->expects($this->once())
			->method('error1');

		// we ensure that we get an error if we can find the file but the file does not contain the class
		$this->assertThat(
			JHtml::_('mocktest2.function1'),
			$this->isFalse()
		);

		JError::setErrorHandling(E_ERROR, 'callback', array($mock1, 'error2'));

		$mock1->expects($this->once())
			->method('error2');

		// we ensure that we get an error if we can't find the file
		$this->assertThat(
			JHtml::_('mocktestnotthere.function1'),
			$this->isFalse()
		);

		JError::setErrorHandling(E_ERROR, 'callback', array($mock1, 'error3'));

		$mock1->expects($this->once())
			->method('error3');

		// we ensure that we get an error if we have the class but not the method
		$this->assertThat(
			JHtml::_('mocktest.nomethod'),
			$this->isFalse()
		);

		// restore our error handlers
		$this->setErrorHandlers($this->savedErrorState);
	}

	/**
	 * @todo Implement testRegister().
	 */
	public function testRegister()
	{
		$registered = $this->getMock('MyHtmlClass', array('mockFunction'));

		// test that we can register the method
		$this->assertThat(
			JHtml::register('prefix.file.testfunction', array($registered, 'mockFunction')),
			$this->isTrue(),
			'Function registers properly'
		);

		// test that calling _ actually calls the function
		$registered->expects($this->once())
			->method('mockFunction');

		JHtml::_('prefix.file.testfunction');

		$this->assertThat(
			JHtml::register('prefix.file.missingtestfunction', array($registered, 'missingFunction')),
			$this->isFalse(),
			'If function is missing, we do not register'
		);
		JHtml::unregister('prefix.file.testfunction');
		JHtml::unregister('prefix.file.missingtestfunction');
	}


	/**
	 * @todo Implement testUnregister().
	 */
	public function testUnregister()
	{
		$registered = $this->getMock('MyHtmlClass', array('mockFunction'));

		// test that we can register the method
		JHtml::register('prefix.file.testfunction', array($registered, 'mockFunction'));

		$this->assertThat(
			JHtml::unregister('prefix.file.testfunction'),
			$this->isTrue(),
			'Function did not unregister'
		);

		$this->assertThat(
			JHtml::unregister('prefix.file.testkeynotthere'),
			$this->isFalse(),
			'Unregister return true when it should have failed'
		);

	}

	/**
	 * @todo Implement testCore().
	 */
	public function testCore()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	public function linkData() {
		return array(
			array(
				'http://www.example.com',
				'Link Text',
				'title="My Link Title"',
				'<a href="http://www.example.com" title="My Link Title">Link Text</a>',
				'Standard link with string attribs failed'
			),
			array(
				'http://www.example.com',
				'Link Text',
				array('title' => 'My Link Title'),
				'<a href="http://www.example.com" title="My Link Title">Link Text</a>',
				'Standard link with array attribs failed'
			)

		);
	}

	/**
	 * @todo Implement testLink().
	 * @dataProvider linkData
	 */
	public function testLink($url, $text, $attribs, $expected, $msg = '')
	{
		$this->assertThat(
			JHtml::link($url, $text, $attribs),
			$this->equalTo($expected),
			$msg
		);
	}

	/**
	 * @todo Implement testImage().
	 */
	public function testImage()
	{
		if(!is_array($_SERVER)) {
			$_SERVER = array();
		}

		// we save the state of $_SERVER for later and set it to appropriate values
		$http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
		$script_name = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : null;
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		// these are some paths to pass to JHtml for testing purposes
		$urlpath = 'test1/';
		$urlfilename = 'image1.jpg';

		// we generate a random template name so that we don't collide or hit anything
		$template = 'mytemplate'.rand(1,10000);

		// we create a stub (not a mock because we don't enforce whether it is called or not)
		// to return a value from getTemplate
		$mock = $this->getMock('myMockObject', array('getTemplate'));
		$mock->expects($this->any())
			->method('getTemplate')
			->will($this->returnValue($template));

		JFactory::$application = $mock;

		// we create the file that JHtml::image will look for
		mkdir(JPATH_THEMES .'/'. $template .'/images/'. $urlpath, 0777, true);
		file_put_contents(JPATH_THEMES .'/'. $template .'/images/'. $urlpath.$urlfilename, 'test');

		// we do a test for the case that the image is in the templates directory
		$this->assertThat(
			JHtml::image($urlpath.$urlfilename, 'My Alt Text', null, true),
			$this->equalTo('<img src="'.JURI::base(true).'/templates/'.$template.'/images/'.$urlpath.$urlfilename.'" alt="My Alt Text"  />'),
			'JHtml::image failed when we should get it from the templates directory'
		);

		$this->assertThat(
			JHtml::image($urlpath.$urlfilename, 'My Alt Text', null, true, true),
			$this->equalTo(JURI::base(true).'/templates/'.$template.'/images/'.$urlpath.$urlfilename),
			'JHtml::image failed in URL only mode when it should come from the templates directory'
		);

		unlink(JPATH_THEMES .'/'. $template .'/images/'. $urlpath.$urlfilename);
		rmdir(JPATH_THEMES .'/'. $template .'/images/'. $urlpath);
		rmdir(JPATH_THEMES .'/'. $template .'/images');
		rmdir(JPATH_THEMES .'/'. $template);

		// we create the file that JHtml::image will look for
		mkdir(JPATH_ROOT .'/media/'. $urlpath .'images', 0777, true);
		file_put_contents(JPATH_ROOT .'/media/'. $urlpath .'images/'. $urlfilename, 'test');

		// we do a test for the case that the image is in the templates directory
		$this->assertThat(
			JHtml::image($urlpath.$urlfilename, 'My Alt Text', null, true),
			$this->equalTo('<img src="'.JURI::base(true).'/media/'.$urlpath.'images/'.$urlfilename.'" alt="My Alt Text"  />'),
			'JHtml::image failed when we should get it from the media directory'
		);

		$this->assertThat(
			JHtml::image($urlpath.$urlfilename, 'My Alt Text', null, true, true),
			$this->equalTo(JURI::base(true).'/media/'.$urlpath.'images/'.$urlfilename),
			'JHtml::image failed when we should get it from the media directory in path only mode'
		);

		unlink(JPATH_ROOT .'/media/'. $urlpath .'images/'. $urlfilename);
		rmdir(JPATH_ROOT .'/media/'. $urlpath .'images');
		rmdir(JPATH_ROOT .'/media/'. $urlpath);

		file_put_contents(JPATH_ROOT .'/media/system/images/'. $urlfilename, 'test');

		$this->assertThat(
			JHtml::image($urlpath.$urlfilename, 'My Alt Text', null, true),
			$this->equalTo('<img src="'.JURI::base(true).'/media/system/images/'.$urlfilename.'" alt="My Alt Text"  />'),
			'JHtml::image failed when we should get it from the media directory'
		);

		$this->assertThat(
			JHtml::image($urlpath.$urlfilename, 'My Alt Text', null, true, true),
			$this->equalTo(JURI::base(true).'/media/system/images/'.$urlfilename),
			'JHtml::image failed when we should get it from the media directory in path only mode'
		);

		unlink(JPATH_ROOT .'/media/system/images/'. $urlfilename);

		$this->assertThat(
			JHtml::image($urlpath.$urlfilename, 'My Alt Text', null, true),
			$this->equalTo('<img src="" alt="My Alt Text"  />'),
			'JHtml::image failed when we should get it from the media directory'
		);

		$this->assertThat(
			JHtml::image($urlpath.$urlfilename, 'My Alt Text', null, true, true),
			$this->equalTo(null),
			'JHtml::image failed when we should get it from the media directory in path only mode'
		);

		$extension = 'testextension';
		$element = 'element';
		$urlpath = 'path1/';
		$urlfilename = 'image1.jpg';

		mkdir(JPATH_ROOT .'/media/'. $extension.'/'.$element .'/images/'. $urlpath, 0777, true);
		file_put_contents(JPATH_ROOT .'/media/'. $extension.'/'.$element .'/images/'. $urlpath.$urlfilename, 'test');

		$this->assertThat(
			JHtml::image($extension.'/'.$element.'/'.$urlpath.$urlfilename, 'My Alt Text', null, true),
			$this->equalTo('<img src="'.JURI::base(true).'/media/'. $extension.'/'.$element .'/images/'. $urlpath.$urlfilename.'" alt="My Alt Text"  />'),
			'JHtml::image failed when we should get it from the media directory, with the plugin fix'
		);

		$this->assertThat(
			JHtml::image($extension.'/'.$element.'/'.$urlpath.$urlfilename, 'My Alt Text', null, true, true),
			$this->equalTo(JURI::base(true).'/media/'. $extension.'/'.$element .'/images/'. $urlpath.$urlfilename),
			'JHtml::image failed when we should get it from the media directory, with the plugin fix path only mode'
		);
		// we remove the file from the media directory
		unlink(JPATH_ROOT .'/media/'. $extension.'/'.$element .'/images/'. $urlpath.$urlfilename);
		rmdir(JPATH_ROOT .'/media/'. $extension.'/'.$element .'/images/'. $urlpath);
		rmdir(JPATH_ROOT .'/media/'. $extension.'/'.$element .'/images');
		rmdir(JPATH_ROOT .'/media/'. $extension.'/'.$element);
		rmdir(JPATH_ROOT .'/media/'. $extension);

		mkdir(JPATH_ROOT .'/media/'. $extension.'/images/'.$element .'/'. $urlpath, 0777, true);
		file_put_contents(JPATH_ROOT .'/media/'. $extension.'/images/'.$element .'/'. $urlpath.$urlfilename, 'test');

		$this->assertThat(
			JHtml::image($extension.'/'.$element.'/'.$urlpath.$urlfilename, 'My Alt Text', null, true),
			$this->equalTo('<img src="'.JURI::base(true).'/media/'.$extension.'/images/'.$element.'/'. $urlpath.$urlfilename.'" alt="My Alt Text"  />')
		);

		$this->assertThat(
			JHtml::image($extension.'/'.$element.'/'.$urlpath.$urlfilename, 'My Alt Text', null, true, true),
			$this->equalTo(JURI::base(true).'/media/'.$extension.'/images/'.$element.'/'.$urlpath.$urlfilename)
		);

		unlink(JPATH_ROOT .'/media/'. $extension.'/images/'.$element .'/'. $urlpath.$urlfilename);
		rmdir(JPATH_ROOT .'/media/'. $extension.'/images/'.$element .'/'. $urlpath);
		rmdir(JPATH_ROOT .'/media/'. $extension.'/images/'.$element);
		rmdir(JPATH_ROOT .'/media/'. $extension.'/images');
		rmdir(JPATH_ROOT .'/media/'. $extension);

		mkdir(JPATH_ROOT .'/media/system/images/'. $element.'/'. $urlpath, 0777, true);
		file_put_contents(JPATH_ROOT .'/media/system/images/'. $element.'/'. $urlpath.$urlfilename, 'test');

		$this->assertThat(
			JHtml::image($extension.'/'.$element.'/'.$urlpath.$urlfilename, 'My Alt Text', null, true),
			$this->equalTo('<img src="'.JURI::base(true).'/media/system/images/'.$element.'/'. $urlpath.$urlfilename.'" alt="My Alt Text"  />')
		);

		$this->assertThat(
			JHtml::image($extension.'/'.$element.'/'.$urlpath.$urlfilename, 'My Alt Text', null, true, true),
			$this->equalTo(JURI::base(true).'/media/system/images/'.$element.'/'.$urlpath.$urlfilename)
		);

		unlink(JPATH_ROOT .'/media/system/images/'. $element.'/'. $urlpath.$urlfilename);
		rmdir(JPATH_ROOT .'/media/system/images/'. $element.'/'. $urlpath);
		rmdir(JPATH_ROOT .'/media/system/images/'. $element);

		$this->assertThat(
			JHtml::image($extension.'/'.$element.'/'.$urlpath.$urlfilename, 'My Alt Text', null, true),
			$this->equalTo('<img src="" alt="My Alt Text"  />')
		);

		$this->assertThat(
			JHtml::image($extension.'/'.$element.'/'.$urlpath.$urlfilename, 'My Alt Text', null, true, true),
			$this->equalTo(null)
		);

		$this->assertThat(
			JHtml::image('http://www.example.com/test/image.jpg', 'My Alt Text',
				array(
					'width' => 150,
					'height' => 150
				)
			),
			$this->equalTo('<img src="http://www.example.com/test/image.jpg" alt="My Alt Text" width="150" height="150" />'),
			'JHtml::image with an absolute path'
		);

		mkdir(JPATH_ROOT .'/test', 0777, true);
		file_put_contents(JPATH_ROOT .'/test/image.jpg', 'test');
		$this->assertThat(
			JHtml::image('test/image.jpg', 'My Alt Text',
				array(
					'width' => 150,
					'height' => 150
				),
				false
			),
			$this->equalTo('<img src="'.JURI::root(true).'/test/image.jpg" alt="My Alt Text" width="150" height="150" />'),
			'JHtml::image with an absolute path, URL does not start with http'
		);
		unlink(JPATH_ROOT .'/test/image.jpg');
		rmdir(JPATH_ROOT .'/test');

		$this->assertThat(
			JHtml::image('test/image.jpg', 'My Alt Text',
				array(
					'width' => 150,
					'height' => 150
				),
				false
			),
			$this->equalTo('<img src="" alt="My Alt Text" width="150" height="150" />'),
			'JHtml::image with an absolute path, URL does not start with http'
		);

		$_SERVER['HTTP_HOST'] = $http_host;
		$_SERVER['SCRIPT_NAME'] = $script_name;

	}

	public function iframeData() {
		return array(
			array(
				'http://www.example.com',
				'Link Text',
				'title="My Link Title"',
				'',
				'<iframe src="http://www.example.com" title="My Link Title" name="Link Text"></iframe>',
				'Iframe with text attribs, no noframes text failed'
			),
			array(
				'http://www.example.com',
				'Link Text',
				array('title' => 'My Link Title'),
				'',
				'<iframe src="http://www.example.com" title="My Link Title" name="Link Text"></iframe>',
				'Iframe with array attribs failed'
			)

		);
	}


	/**
	 * @todo Implement testIframe().
	 * @dataProvider iframeData
	 */
	public function testIframe($url, $name, $attribs, $noFrames, $expected, $msg = '')
	{
		$this->assertThat(
			JHtml::iframe($url, $name, $attribs, $noFrames),
			$this->equalTo($expected),
			$msg
		);
	}

	/**
	 * @todo Implement testScript().
	 */
	public function testScript()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testSetFormatOptions().
	 */
	public function testSetFormatOptions()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testImage().
	 */
	public function testStylesheet()
	{
		if(!is_array($_SERVER)) {
			$_SERVER = array();
		}

		// we save the state of $_SERVER for later and set it to appropriate values
		$http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
		$script_name = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : null;
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		// these are some paths to pass to JHtml for testing purposes
		$extension = 'testextension';
		$element = 'element';
		$cssfilename = 'stylesheet.css';


		// we generate a random template name so that we don't collide or hit anything
		$template = 'mytemplate'.rand(1,10000);

		// we create a stub (not a mock because we don't enforce whether it is called or not)
		// to return a value from getTemplate
		$mock = $this->getMock('myMockObject', array('getTemplate'));
		$mock->expects($this->any())
			->method('getTemplate')
			->will($this->returnValue($template));

		JFactory::$application = $mock;

		// we create the file that JHtml::image will look for
		mkdir(JPATH_THEMES .'/'. $template .'/css/'.$extension, 0777, true);
		file_put_contents(JPATH_THEMES .'/'. $template .'/css/'.$extension.'/'.$cssfilename, 'test');

		$docMock1 = $this->getMock('myMockDoc1', array('addStylesheet'));

		$docMock1->expects($this->once())
			->method('addStylesheet')
			->with(
				JURI::base(true).'/templates/'.$template.'/css/'.$extension.'/'.$cssfilename,
				'text/css',
				null,
				null
		);

		JFactory::$document = $docMock1;

		// we can't directly assert anything about the return value because it doesn't return anything
		JHtml::stylesheet($extension.'/'.$cssfilename, null, true);

		$this->assertThat(
			JHtml::stylesheet($extension.'/'.$cssfilename, null, true, true),
			$this->equalTo(JURI::base(true).'/templates/'.$template.'/css/'.$extension.'/'.$cssfilename),
			'Stylesheet in the template directory failed'
		);

		unlink(JPATH_THEMES .'/'. $template .'/css/'.$extension.'/'. $cssfilename);
		rmdir(JPATH_THEMES .'/'. $template .'/css/'.$extension);
		rmdir(JPATH_THEMES .'/'. $template .'/css');
		rmdir(JPATH_THEMES .'/'. $template);

		$docMock2 = $this->getMock('myMockDoc2', array('addStylesheet'));

		$docMock2->expects($this->once())
			->method('addStylesheet')
			->with(
				JURI::base(true).'/media/system/css/modal.css',
				'text/css',
				null,
				null
		);

		JFactory::$document = $docMock2;

		JHtml::stylesheet($extension.'/modal.css', null, true);

		file_put_contents(JPATH_ROOT .'/media/system/css/'.$cssfilename, 'test');
		$this->assertThat(
			JHtml::stylesheet($extension.'/'.$cssfilename, null, true, true),
			$this->equalTo(JURI::root(true).'/media/system/css/'.$cssfilename),
			'Stylesheet in the media directory failed - path only'
		);
		unlink(JPATH_ROOT .'/media/system/css/'.$cssfilename);

		// we create the file that JHtml::stylesheet will look for
		mkdir(JPATH_ROOT .'/media/'.$extension.'/'.$element.'/css/', 0777, true);
		file_put_contents(JPATH_ROOT .'/media/'.$extension.'/'.$element.'/css/'.$cssfilename, 'test');

		$this->assertThat(
			JHtml::stylesheet($extension.'/'.$element.'/'.$cssfilename, null, true, true),
			$this->equalTo(JURI::root(true).'/media/'.$extension.'/'.$element.'/css/'.$cssfilename),
			'Stylesheet in the media directory -plugins group code - failed - path only'
		);

		unlink(JPATH_ROOT .'/media/'.$extension.'/'.$element.'/css/'.$cssfilename);
		rmdir(JPATH_ROOT .'/media/'.$extension.'/'.$element.'/css/');
		rmdir(JPATH_ROOT .'/media/'.$extension.'/'.$element);
		rmdir(JPATH_ROOT .'/media/'.$extension);

		mkdir(JPATH_ROOT .'/media/system/css/'.$element, 0777, true);
		file_put_contents(JPATH_ROOT .'/media/system/css/'.$element.'/'.$cssfilename, 'test');
		$this->assertThat(
			JHtml::stylesheet($extension.'/'.$element.'/'.$cssfilename, null, true, true),
			$this->equalTo(JURI::root(true).'/media/system/css/'.$element.'/'.$cssfilename),
			'Stylesheet in the media directory -plugins group code - failed - path only'
		);
		unlink(JPATH_ROOT .'/media/system/css/'.$element.'/'.$cssfilename);
		rmdir(JPATH_ROOT .'/media/system/css/'.$element);

		// we create the file that JHtml::stylesheet will look for
		mkdir(JPATH_ROOT .'/media/'.$extension.'/css/'.$element, 0777, true);
		file_put_contents(JPATH_ROOT .'/media/'.$extension.'/css/'.$element.'/'.$cssfilename, 'test');

		$this->assertThat(
			JHtml::stylesheet($extension.'/'.$element.'/'.$cssfilename, null, true, true),
			$this->equalTo(JURI::root(true).'/media/'.$extension.'/css/'.$element.'/'.$cssfilename),
			'Stylesheet in the media directory -plugins group code - failed - path only'
		);

		unlink(JPATH_ROOT .'/media/'.$extension.'/css/'.$element.'/'.$cssfilename);
		rmdir(JPATH_ROOT .'/media/'.$extension.'/css/'.$element);
		rmdir(JPATH_ROOT .'/media/'.$extension.'/css');
		rmdir(JPATH_ROOT .'/media/'.$extension);

		$docMock3 = $this->getMock('myMockDoc3', array('addStylesheet'));

		$docMock3->expects($this->once())
			->method('addStylesheet')
			->with(
				JURI::root(true).'/media/system/css/modal.css',
				'text/css',
				null,
				'media="print" title="sample title"'
		);

		JFactory::$document = $docMock3;

		JHtml::stylesheet('media/system/css/modal.css', array('media' => 'print', 'title' => 'sample title'));

		$_SERVER['HTTP_HOST'] = $http_host;
		$_SERVER['SCRIPT_NAME'] = $script_name;

	}

	/**
	 * @todo Implement testDate().
	 */
	public function testDate()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}

	/**
	 * @todo Implement testTooltip().
	 */
	public function testTooltip()
	{
		if(!is_array($_SERVER)) {
			$_SERVER = array();
		}

		// we save the state of $_SERVER for later and set it to appropriate values
		$http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
		$script_name = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : null;
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		// we generate a random template name so that we don't collide or hit anything
		$template = 'mytemplate'.rand(1,10000);

		// we create a stub (not a mock because we don't enforce whether it is called or not)
		// to return a value from getTemplate
		$mock = $this->getMock('myMockObject', array('getTemplate'));
		$mock->expects($this->any())
			->method('getTemplate')
			->will($this->returnValue($template));

		JFactory::$application = $mock;

		// Testing classical cases
		$this->assertThat(
			JHtml::tooltip('Content'),
			$this->equalTo('<span class="hasTip" title="Content"><img src="'.JURI::base(true).'/media/system/images/tooltip.png" alt="Tooltip"  /></span>'),
			'Basic tooltip failed'
		);

		$this->assertThat(
			JHtml::tooltip('Content','Title'),
			$this->equalTo('<span class="hasTip" title="Title::Content"><img src="'.JURI::base(true).'/media/system/images/tooltip.png" alt="Tooltip"  /></span>'),
			'Tooltip with title and content failed'
		);

		$this->assertThat(
			JHtml::tooltip('Content','Title',null,'Text'),
			$this->equalTo('<span class="hasTip" title="Title::Content">Text</span>'),
			'Tooltip with title and content and text failed'
		);

		$this->assertThat(
			JHtml::tooltip('Content','Title',null,'Text','http://www.monsite.com'),
			$this->equalTo('<span class="hasTip" title="Title::Content"><a href="http://www.monsite.com">Text</a></span>'),
			'Tooltip with title and content and text and href failed'
		);

		$this->assertThat(
			JHtml::tooltip('Content','Title','tooltip.png',null,null,'MyAlt'),
			$this->equalTo('<span class="hasTip" title="Title::Content"><img src="'.JURI::base(true).'/media/system/images/tooltip.png" alt="MyAlt"  /></span>'),
			'Tooltip with title and content and alt failed'
		);

		$this->assertThat(
			JHtml::tooltip('Content','Title','tooltip.png',null,null,'MyAlt','hasTip2'),
			$this->equalTo('<span class="hasTip2" title="Title::Content"><img src="'.JURI::base(true).'/media/system/images/tooltip.png" alt="MyAlt"  /></span>'),
			'Tooltip with title and content and alt and class failed'
		);

		// Testing where title is an array
		$this->assertThat(
			JHtml::tooltip('Content',array('title'=>'Title')),
			$this->equalTo('<span class="hasTip" title="Title::Content"><img src="'.JURI::base(true).'/media/system/images/tooltip.png" alt="Tooltip"  /></span>'),
			'Tooltip with title and content failed'
		);

		$this->assertThat(
			JHtml::tooltip('Content',array('title'=>'Title','text'=>'Text')),
			$this->equalTo('<span class="hasTip" title="Title::Content">Text</span>'),
			'Tooltip with title and content and text failed'
		);

		$this->assertThat(
			JHtml::tooltip('Content',array('title'=>'Title','text'=>'Text','href'=>'http://www.monsite.com')),
			$this->equalTo('<span class="hasTip" title="Title::Content"><a href="http://www.monsite.com">Text</a></span>'),
			'Tooltip with title and content and text and href failed'
		);

		$this->assertThat(
			JHtml::tooltip('Content',array('title'=>'Title','alt'=>'MyAlt')),
			$this->equalTo('<span class="hasTip" title="Title::Content"><img src="'.JURI::base(true).'/media/system/images/tooltip.png" alt="MyAlt"  /></span>'),
			'Tooltip with title and content and alt failed'
		);
		$this->assertThat(
			JHtml::tooltip('Content',array('title'=>'Title','class'=>'hasTip2')),
			$this->equalTo('<span class="hasTip2" title="Title::Content"><img src="'.JURI::base(true).'/media/system/images/tooltip.png" alt="Tooltip"  /></span>'),
			'Tooltip with title and content and class failed'
		);
	}

	/**
	 * Tests JHTML::calendar() method with and without 'readonly' attribute.
	 */
	public function testCalendar()
	{
		// Create a world for the test
		jimport('joomla.session.session');
		jimport('joomla.application.application');
		jimport('joomla.document.document');

		$cfg = new JObject();
		JFactory::$session = $this->getMock('JSession', array('_start'));
		JFactory::$application = $this->getMock('ApplicationMock');
		JFactory::$config = $cfg;

		JFactory::$application->expects($this->any())
								->method('getTemplate')
								->will($this->returnValue('atomic'));

		$cfg->live_site = 'http://example.com';
		$cfg->offset = 'Europe/Kiev';
		$_SERVER['HTTP_USER_AGENT'] = 'Test Browser';

		// two sets of test data
		$test_data = array('date' => '2010-05-28', 'friendly_date' => 'Friday, 28 May 2010', 
					  'name' => 'cal1_name', 'id' => 'cal1_id', 'format' => '%Y-%m-%d', 
					  'attribs' => array()
				);

		$test_data_ro = array_merge($test_data, array('attribs' => array('readonly' => 'readonly')));

		foreach (array($test_data, $test_data_ro) as $data) {
			// Reset the document
			JFactory::$document = JDocument::getInstance('html', array('unique_key' => serialize($data)));

			$input = JHTML::calendar($data['date'], $data['name'], $data['id'], $data['format'], $data['attribs']);
			$this->assertThat(
				strlen($input),
				$this->greaterThan(0),
				'Line:'.__LINE__.' The calendar method should return something without error.'
			);

			$xml = new simpleXMLElement('<calendar>' . $input . '</calendar>');
			$this->assertEquals(
				(string) $xml->input['type'],
				'text',
				'Line:'.__LINE__.' The calendar input should have `type == "text"`'
			);

			$this->assertEquals(
				(string) $xml->input['title'],
				$data['friendly_date'],
				'Line:'.__LINE__.' The calendar input should have `title == "' . $data['friendly_date'] . '"`'
			);

			$this->assertEquals(
				(string) $xml->input['name'],
				$data['name'],
				'Line:'.__LINE__.' The calendar input should have `name == "' . $data['name'] . '"`'
			);

			$this->assertEquals(
				(string) $xml->input['id'],
				$data['id'],
				'Line:'.__LINE__.' The calendar input should have `id == "' . $data['id'] . '"`'
			);

			$this->assertEquals(
				(string) $xml->input['value'],
				$data['date'],
				'Line:'.__LINE__.' The calendar input should have `value == "' . $data['date'] . '"`'
			);

			$head_data = JFactory::getDocument()->getHeadData();

			if (isset($data['attribs']['readonly']) && $data['attribs']['readonly'] === 'readonly') {
				$this->assertEquals(
					(string) $xml->input['readonly'],
					$data['attribs']['readonly'],
					'Line:'.__LINE__.' The readonly calendar input should have `readonly == "' . $data['attribs']['readonly'] . '"`'
				);

				$this->assertFalse(
					isset($xml->img),
					'Line:'.__LINE__.' The readonly calendar input shouldn\'t have a calendar image'
				);

				$this->assertArrayNotHasKey(
					'/media/system/js/calendar.js',
					$head_data['scripts'],
					'Line:'.__LINE__.' JS file "calendar.js" shouldn\'t be loaded'
				);

				$this->assertArrayNotHasKey(
					'/media/system/js/calendar-setup.js',
					$head_data['scripts'],
					'Line:'.__LINE__.' JS file "calendar-setup.js" shouldn\'t be loaded'
				);

				$this->assertArrayNotHasKey(
					'text/javascript',
					$head_data['script'],
					'Line:'.__LINE__.' Inline JS for the calendar shouldn\'t be loaded'
				);
			}
			else {
				$this->assertFalse(
					isset($xml->input['readonly']),
					'Line:'.__LINE__.' The calendar input shouldn\'t have readonly attribute'
				);

				$this->assertTrue(
					isset($xml->img),
					'Line:'.__LINE__.' The calendar input should have a calendar image'
				);

				$this->assertEquals(
					(string) $xml->img['id'],
					$data['id'] . '_img',
					'Line:'.__LINE__.' The calendar image should have `id == "' . $data['id'] . '_img' . '"`'
				);

				$this->assertEquals(
					(string) $xml->img['class'],
					'calendar',
					'Line:'.__LINE__.' The calendar image should have `class == "calendar"`'
				);

				$this->assertFileExists(
					JPATH_ROOT . $xml->img['src'],
					'Line:'.__LINE__.' The calendar image source should point to an existent file'
				);

				$this->assertArrayHasKey(
					'/media/system/js/calendar.js',
					$head_data['scripts'],
					'Line:'.__LINE__.' JS file "calendar.js" should be loaded'
				);

				$this->assertArrayHasKey(
					'/media/system/js/calendar-setup.js',
					$head_data['scripts'],
					'Line:'.__LINE__.' JS file "calendar-setup.js" should be loaded'
				);

				$this->assertContains(
					'DHTML Date/Time Selector',
					$head_data['script']['text/javascript'],
					'Line:'.__LINE__.' Inline JS for the calendar should be loaded'
				);
			}
		}
	}

	/**
	 * @todo Implement testAddIncludePath().
	 */
	public function testAddIncludePath()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
		'This test has not been implemented yet.'
		);
	}
}


class ApplicationMock
{
	public function getTemplate()
	{

	}
}
