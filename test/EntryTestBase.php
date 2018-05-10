<?php

namespace ExifEye\Test\core;

use ExifEye\core\ElementInterface;

class EntryTestBase extends ExifEyeTestCaseBase
{
    protected $mockParentElement;

    public function setUp()
    {
        parent::setUp();
        $this->mockParentElement = $this->getMockBuilder(ElementInterface::class)->getMock();
        $this->mockParentElement->method('xxgetDoc')->willReturn(null);
    }
}
