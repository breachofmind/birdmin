<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \Birdmin\Components\Button;
use Birdmin\Components\ButtonGroup;
use Birdmin\Page;

class ComponentTest extends TestCase
{
    public function test_button_components()
    {
        // Test object
        $page = Page::find(1);

        // Check the create method.
        $group = ButtonGroup::create();
        $this->assertInstanceOf(ButtonGroup::class, $group);
        // Shouldn't have anything in it.
        $this->assertCount(0, $group->getButtons());
        // Should return a zero count.
        $this->assertEquals(0, $group->count());

        // Button groups
        $button = Button::create();
        // Check default setting.
        $this->assertNull($button->getAttribute('href'));

        // Adding to the group will increment the button count.
        $group->add($button);
        $this->assertEquals(1, $group->count());

        // Try adding the other way around.
        $button2 = Button::create();
        $button2->addTo($group);
        $this->assertEquals(2, $group->count());

        // Check if the button attributes are assigned correctly.
        $button->parent($page)->link('view');
        $this->assertEquals("View Pages", $button->getLabel());
        $this->assertEquals($page::getIcon(), $button->getIcon());
        $this->assertEquals(cms_url($page::plural()), $button->getAttribute('href'));

    }
}
