<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;

/**
 * @see \App\Http\Requests\StoreStatement1GoogleRequest
 */
class StoreStatement1GoogleRequestTest extends TestCase
{
    /** @var \App\Http\Requests\StoreStatement1GoogleRequest */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new \App\Http\Requests\StoreStatement1GoogleRequest();
    }

    /**
     * @test
     */
    public function rules(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $actual = $this->subject->rules();

        $this->assertValidationRules([
            'file' => [
                'required',
            ],
        ], $actual);
    }

    // test cases...
}
