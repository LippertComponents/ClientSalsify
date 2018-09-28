<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 9/28/18
 * Time: 5:25 AM
 */

use LCI\Salsify\Helpers\HTML;

class HTMLHelperTest extends BaseTestCase
{

    protected $simple_string = 'This is a simple string line';

    protected $list_items = [
        'list item 1',
        'list item 2',
        'list item 3',
        'list item 4',
        'list item 5',
    ];

    protected $expected_list_items = '<li>list item 1</li><li>list item 2</li><li>list item 3</li><li>list item 4</li><li>list item 5</li>';

    protected $expected_wrapped_list_items = '<ul><li>list item 1</li><li>list item 2</li><li>list item 3</li><li>list item 4</li><li>list item 5</li></ul>';

    protected $expected_limited_list_items = '<ul><li>list item 1</li><li>list item 2</li></ul>';

    protected $expected_pretty_list_items = '<ul>
<li>
list item 1
</li>
<li>
list item 2
</li>
<li>
list item 3
</li>
<li>
list item 4
</li>
<li>
list item 5
</li>
</ul>';

    protected $paragraphs = [
        'Omnis accusantium ipsa vel neque in. Nihil voluptatum laudantium ducimus recusandae ut. In qui accusamus ratione.',
        'Qui ratione enim autem ea. Nihil autem et eligendi ea rerum in saepe. Illum omnis nulla aut. '.
            'Sit repellendus sit voluptatibus facilis veniam magnam quae quia. Qui eos beatae repellendus sint unde '.
            'omnis in. Quia ipsa fuga ut ut veritatis incidunt reprehenderit quibusdam.',
        'Nobis voluptates ipsum similique officia quos ut esse quam. Voluptate molestias eum unde repellendus '.
            'voluptatem ea. Nisi numquam dignissimos et. Et corporis repudiandae velit laboriosam voluptate quod. '.
            'Eum officiis et id amet sunt ut aut.',
        'Non unde nam doloribus. Est repudiandae nulla minus. Beatae nihil qui blanditiis praesentium est qui. Ut '.
            'excepturi sint eos. Vero tempore quia odio sed ut. Voluptatem provident dolor rem quidem quisquam dolor aut aut.',
        'Enim iure repudiandae fugit voluptatum qui deserunt. Maiores qui at voluptatem voluptate iste recusandae. '.
            'Ut ut aspernatur consectetur sed ut placeat doloribus sequi. Hic in numquam consectetur.'
    ];

    public function testCreateHTMLHelperInstance()
    {
        $htmlHelper = new HTML('simple string');

        $this->assertInstanceOf(
            '\LCI\Salsify\Helpers\HTML',
            $htmlHelper,
            'Initializing Helpers/HTML simple string failed'
        );

        $htmlHelper = new HTML(['list item 1', 'list item 2', 'list item 3']);

        $this->assertInstanceOf(
            '\LCI\Salsify\Helpers\HTML',
            $htmlHelper,
            'Initializing Helpers/HTML with list items failed'
        );
    }

    /**
     * @depends testCreateHTMLHelperInstance
     */
    public function testCanMakeHTMLHeading()
    {
        $htmlHelper = new HTML($this->simple_string);
        $htmlHelper
            ->setItemInHTMLElement('h1');

        $this->assertEquals(
            '<h1>' . $this->simple_string . '</h1>',
            $htmlHelper->renderAsHTML(),
            'Helpers/HTML make simple string as HTML h1 heading failed'
        );

        // now in Pretty HTML
        $this->assertEquals(
            '<h1>' . PHP_EOL . $this->simple_string . PHP_EOL . '</h1>',
            $htmlHelper->makePrettyHtml()->renderAsHTML(),
            'Helpers/HTML make simple string as pretty HTML h1 heading failed'
        );

    }

    /**
     * @depends testCreateHTMLHelperInstance
     */
    public function testCanMakeHTMLLists()
    {
        $htmlHelper = new HTML($this->list_items);

        $list = $htmlHelper
            ->makeListItems()
            ->renderAsHTML();

        $this->assertEquals(
            $this->expected_list_items,
            $list,
            'Helpers/HTML make array as HTML li failed'
        );

        $list = $htmlHelper
            ->makeListItems()
            ->wrapListItems()
            ->renderAsHTML();

        $this->assertEquals(
            $this->expected_wrapped_list_items,
            $list,
            'Helpers/HTML make array as HTML ul->li failed'
        );

        // now in Pretty HTML
        $list = $htmlHelper
            ->makePrettyHtml()
            ->renderAsHTML();

        $this->assertEquals(
            $this->expected_pretty_list_items,
            $list,
            'Helpers/HTML make array as pretty HTML ul->li failed'
        );

        // now limit to only 2 list items
        $list = $htmlHelper
            ->makePrettyHtml(false)
            ->setLimit(2)
            ->renderAsHTML();

        $this->assertEquals(
            $this->expected_limited_list_items,
            $list,
            'Helpers/HTML make array as pretty HTML ul->li failed'
        );
    }

    /**
     * @depends testCreateHTMLHelperInstance
     */
    public function testCanMakeHTMLParagraphs()
    {
        $htmlHelper = new HTML($this->paragraphs);

        $paragraphs = $htmlHelper
            ->makeParagraphs()
            ->renderAsHTML();

        $this->assertEquals(
            file_get_contents(__DIR__ . '/expected/paragraphs.html'),
            $paragraphs,
            'Helpers/HTML make array as HTML paragraph failed'
        );

        // wrap paragraphs in a section
        $paragraphs = $htmlHelper
            ->makeParagraphs()
            ->wrapItemInHTMLElement('section')
            ->renderAsHTML();

        $this->assertEquals(
            file_get_contents(__DIR__ . '/expected/paragraphs_in_section.html'),
            $paragraphs,
            'Helpers/HTML make array as HTML section->p failed'
        );

        // now in Pretty HTML
        $paragraphs = $htmlHelper
            ->makePrettyHtml()
            ->renderAsHTML();

        $this->assertEquals(
            file_get_contents(__DIR__ . '/expected/paragraphs_in_section_pretty.html'),
            $paragraphs,
            'Helpers/HTML make array as pretty HTML section->p failed'
        );

        // now limit to only 1 paragraph an no wrapping element
        $paragraphs = $htmlHelper
            ->makePrettyHtml(false)
            ->wrapItemInHTMLElement('')
            ->setLimit(1)
            ->renderAsHTML();

        $this->assertEquals(
            file_get_contents(__DIR__ . '/expected/paragraph_single.html'),
            $paragraphs,
            'Helpers/HTML make array as a single HTML paragraph failed'
        );
    }

}